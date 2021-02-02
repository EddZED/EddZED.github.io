<?php
class ModelCatalogSimplePars extends Model {

############################################################################################
############################################################################################
#						Создание проктов. Начальные настройки
############################################################################################
############################################################################################

#получение id доноров
public function getAllProject(){
	$pars_settings = $this->db->query("SELECT `dn_id`, `dn_name` FROM " . DB_PREFIX . "pars_setting ORDER BY dn_id ASC");
	return $pars_settings;
}

//создаем все базы данных которых нехватает.
public function madeBd($dn_id){
	//проверяем таблицу браузер
	$browser = $this->db->query("SELECT id FROM `".DB_PREFIX."pars_browser` WHERE `dn_id`=".(int)$dn_id);
	if($browser->num_rows == 0){
		$this->createDbBrowser($dn_id);
	}
}
#Создание донора.
public function DnAdd($data){
	$data = htmlspecialchars($data);

	if(!empty($data)){
		//определяем версию движка
		$engine = $this->checkEngine();

		$this->db->query("INSERT INTO `" . DB_PREFIX . "pars_setting` SET
			`dn_name` ='".$this->db->escape($data)."',
			`vers_op`='".$this->db->escape($engine)."'");
		$dn_id = $this->db->getLastId();

		#Создаем таблицу Prsetup
		$this->createDbPrsetup($dn_id);
		#Создаем таблицу браузера
		$this->createDbBrowser($dn_id);

		//проверяем есть ли группа атрибутов. Если нет создаем.
		$attr_group_id = $this->db->query("SELECT attribute_group_id FROM `" . DB_PREFIX . "attribute_group_description` WHERE attribute_group_id =1");
		if($attr_group_id->num_rows == 0){
			$this->db->query("INSERT INTO `" . DB_PREFIX . "attribute_group` SET `attribute_group_id`=1, `sort_order` = 0");

			//получаем списко используемых языков
			$lang = $this->db->query("SELECT * FROM ".DB_PREFIX."language ORDER BY `language_id` ASC");
			$langs = $lang->rows;
			//Для всех языков прописываем группу.
			foreach ($langs as $key => $lang) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "attribute_group_description` SET `attribute_group_id` =1, `language_id`=".(int)$lang['language_id'].",`name`=''");
			}

		}
	}else{
		$this->session->data['error'] = 'Не задано имя проекта';
	}
}

#Удаление донора
public function DnDel($data){
	foreach($data as $dn_id){

		//получаем id всех границ парсинга для удаления их файлов пред просмотра.
		$param_id = $this->db->query("SELECT `id` FROM ".DB_PREFIX."pars_param WHERE `dn_id` =".(int)$dn_id);
		$param_id = $param_id->rows;
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_setting` WHERE `dn_id` =".(int)$dn_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_sen_link` WHERE `dn_id` =".(int)$dn_id);
    $this->db->query("DELETE FROM `" . DB_PREFIX . "pars_link` WHERE `dn_id` =".(int)$dn_id);
    $this->db->query("DELETE FROM `" . DB_PREFIX . "pars_param` WHERE `dn_id` =".(int)$dn_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_createcsv` WHERE `dn_id` =".(int)$dn_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_prsetup` WHERE `dn_id` =".(int)$dn_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_replace` WHERE `dn_id` =".(int)$dn_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_browser` WHERE `dn_id` =".(int)$dn_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_proxy_list` WHERE `dn_id` =".(int)$dn_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_cron_list` WHERE `dn_id` =".(int)$dn_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_link_list` WHERE `dn_id` =".(int)$dn_id);


		//Удаляем лог файл
		$path_log = DIR_LOGS."simplepars_id-".$dn_id.".log";
		if (file_exists($path_log)) {
			unlink($path_log);
		}
		
		$path_cookies = DIR_APPLICATION.'simplepars/cookie/cookie_'.$dn_id.'.txt';
		if (file_exists($path_cookies)) {
			unlink($path_cookies);
		}
		
		//Удаление файлов кеша, поиск замены.
		foreach ($param_id as $param) {
			$file_param = DIR_APPLICATION.'simplepars/replace/'.$param['id'];
			//Проверяем есть ли такой фаил
			if (file_exists($file_param.'_input_arr.txt')) { unlink($file_param.'_input_arr.txt'); }
			if (file_exists($file_param.'_input_text.txt')) { unlink($file_param.'_input_text.txt'); }
			if (file_exists($file_param.'_output.txt')) { unlink($file_param.'_output.txt'); }
		}

		//Удаление кеша страниц донора.
		$this->urlDelAllCache($dn_id);
	}
}

############################################################################################
############################################################################################
#						Страница сбора ссылок, и работа ссылками.
############################################################################################
############################################################################################

//Добвляем ссылки в очередь парсинга
public function AddParsSenLink($link='', $dn_id){
	$link = $this->ClearLink($link);
	$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "pars_sen_link` SET
		`link` ='".$this->db->escape($link)."',
		`key_md5` ='".md5($dn_id.$link)."',
		`dn_id`=".(int)$dn_id);
}

//Добвляем ссылки в выдачу
public function AddParsLink($link='', $dn_id){
	$link = $this->ClearLink($link);
	$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "pars_link` SET
		`link` ='".$this->db->escape($link)."',
		`key_md5` ='".md5($dn_id.$link)."',
		`dn_id`=".(int)$dn_id
	);

}

//Удалить ссылки очереди
public function DelParsSenLink($dn_id){
	$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_sen_link` WHERE `dn_id` =".(int)$dn_id);
}

//Удалить ссылки выдачи
public function DelParsLink($dn_id){

	$this->db->query("DELETE FROM `" . DB_PREFIX . "pars_link` WHERE `dn_id` =".(int)$dn_id);
}

#Остановка парсинга сбора ссылок
public function StopParsLink($dn_id){
	$this->db->query("UPDATE `" . DB_PREFIX . "pars_setting` SET `pars_stop`=0 WHERE `dn_id`=".(int)$dn_id);
}

//пометить ссылки как непросканированные.
public function linksRestart($dn_id){

	$this->db->query("UPDATE `" . DB_PREFIX . "pars_link` SET `scan`=1 WHERE `dn_id`=".(int)$dn_id);
}

//пометить ссылки очереди как непросканированные.
public function linksSenRestart($dn_id){
	$this->db->query("UPDATE `" . DB_PREFIX . "pars_sen_link` SET `scan`=1 WHERE `dn_id`=".(int)$dn_id);
}

#Фунция очистки ссылок от всякого
public function ClearLink($link){
	$link = htmlspecialchars_decode(trim($link));
	return $link;
}

#Вывод содержимого страницы grab
public function ViemGrab($dn_id){
	#получаем все настройки донора
	$setting = $this->getSetting($dn_id);
	#$this->wtfarrey($setting);
	#получаем ссылки очереди сканиарования
	$round_links = $this->db->query("SELECT * FROM " . DB_PREFIX . "pars_sen_link WHERE scan = 1 AND `dn_id`=".(int)$dn_id." LIMIT 0,".$setting['page_cou_link']);
	$round_links_count = $this->db->query("SELECT COUNT(*) as count FROM " . DB_PREFIX . "pars_sen_link WHERE scan = 1 AND `dn_id`=".(int)$dn_id);
	$round_links_count = $round_links_count->row['count'];

	#получаем количество просканированных ссылок, и очереди
	$count_finish_scan = $this->db->query("SELECT COUNT(*) as count FROM " . DB_PREFIX . "pars_sen_link WHERE scan = 0 AND `dn_id`=".(int)$dn_id);
	$count_finish_scan = $count_finish_scan->row['count'];

	#получаем ссылки выдачи
	$finish_links = $this->db->query("SELECT * FROM " . DB_PREFIX . "pars_link WHERE `dn_id`=".(int)$dn_id." ORDER BY id ASC LIMIT 0,".$setting['page_cou_link']);
	$finish_links_count = $this->db->query("SELECT COUNT(*) as count FROM " . DB_PREFIX . "pars_link WHERE `dn_id`=".(int)$dn_id);
	$finish_links_count = $finish_links_count->row['count'];

	$browser = $this->db->query("SELECT * FROM `". DB_PREFIX ."pars_browser` WHERE dn_id=".(int)$dn_id);
	$viemgrab['browser'] = $browser->row;

	#Привожу в удобный вид для отображения
	$viemgrab['round_links_prepare'] = $round_links_count;
	$round_link = '';
	foreach ($round_links->rows as $key => $value) {
		if ($key == 0) { $round_link = $value['link'];	} elseif ($key == $setting['page_cou_link']) { break;} else { $round_link .= PHP_EOL.$value['link']; }
	}

	#Привожу в удобный вид для отображения
	$viemgrab['links_prepare'] = $finish_links_count;
	$finish_link = '';
	foreach ($finish_links->rows as $key => $value) {
		if ($key == 0) { $finish_link = $value['link'];	} elseif ($key == $setting['page_cou_link']) { break;} else { $finish_link .= PHP_EOL.$value['link']; }
	}

	//составляем в удобном виде для вывода границ парсинга
	$raund_param = explode('{!na!}', $setting['filter_round_param']);
	if(empty($raund_param[0])){
		$raund_param[0] = '';
	}
	if(empty($raund_param[1])){
		$raund_param[1] = '';
	}

	$link_param = explode('{!na!}', $setting['filter_link_param']);
	if(empty($link_param[0])){
		$link_param[0] = '';
	}
	if(empty($link_param[1])){
		$link_param[1] = '';
	}

	//пролизводим проверку есть ли дериктория кеша. Если нет создаем.
	$cache_dir = DIR_APPLICATION.'simplepars/cache_page/'.$dn_id.'/';
	if(!is_dir($cache_dir)){ mkdir($cache_dir, 0755, true); }

	$setting['link_param_start'] = $link_param[0];
	$setting['link_param_stop'] = $link_param[1];
	$setting['round_param_start'] = $raund_param[0];
	$setting['round_param_stop'] = $raund_param[1];
	
	#Составляю массв для рендеринга. num_rows
	$viemgrab['setting'] = $setting;
	$viemgrab['round_link'] = $round_link;

	$viemgrab['finish_link'] = $finish_link;
	$viemgrab['count_finish_scan'] = $count_finish_scan;
	$viemgrab['greb_cout_sen_link'] = $round_links_count;
	#$this->wtfarrey($viemgrab);
	return $viemgrab;
}

#Повторная фильтрация списка.
public function UseNewFilter($dn_id, $who){
	//Получаем настройки.
	$setting = $this->getSetting($dn_id);

  if($who == 'filter_round'){
   $data_links = $this->db->query("SELECT `link` FROM `". DB_PREFIX ."pars_sen_link` WHERE dn_id=".(int)$dn_id);
  }elseif($who == 'filter_link') {
   $data_links = $this->db->query("SELECT `link` FROM `". DB_PREFIX ."pars_link` WHERE dn_id=".(int)$dn_id);
  }
  $data_links = $data_links->rows;
  
  if(!empty($data_links)){
   if($who == 'filter_round'){
    $this->DelParsSenLink($dn_id);
   }elseif($who == 'filter_link'){
    $this->DelParsLink($dn_id);
   }
   foreach($data_links as $var){
    $links[] = $var['link'];
   }
   $this->filterLink($links, $setting, $dn_id, $who);
  }
}

#Сохранение настроек сбора ссылок
public function SeveFormGrab($data, $dn_id){
	#$this->wtfarrey($data);
	$data['dn_name'] = htmlspecialchars($data['dn_name']);
	if(empty($data['start_link'])){
		$data['start_link'] = '';
	}else{
		if(preg_match('#^http[s]?\:\/\/(.*)[.]#i', $data['start_link'])){
			$data['start_link'] = $this->ClearLink($data['start_link']);
		}else{
			$data['start_link'] = '';
			$this->session->data['error'] = ' Стартовая ссылка должна содержать протокол. http:// или https://';
		}
	}
	if(empty($data['page_cou_link'])){ $data['page_cou_link'] = 5000; }
	if(empty($data['filter_round_yes'])){	$data['filter_round_yes'] = ''; }
	if(empty($data['filter_round_no'])){ $data['filter_round_no'] = ''; }
	if(empty($data['filter_link_yes'])){ $data['filter_link_yes'] = ''; }
	if(empty($data['filter_link_no'])){	$data['filter_link_no'] = ''; }
	if(empty($data['filter_round_method'])){ $data['filter_round_method'] = 'or'; }
	if(empty($data['filter_link_method'])){	$data['filter_link_method'] = 'or'; }
	if(empty($data['pars_pause'])){	$data['pars_pause'] = 0; }
	if(empty($data['type_grab'])){ $data['type_grab'] = 1; }
	if(empty($data['thread'])){ $data['thread'] = 1; }

	if(empty($data['round_param_start'])){ $data['round_param_start'] = ''; }
	if(empty($data['round_param_stop'])){	$data['round_param_stop'] = ''; }
	if(empty($data['filter_round_depth'])){	$data['filter_round_depth'] = ''; }
	if(empty($data['filter_round_slash'])){	$data['filter_round_slash'] = 0; }
	if(empty($data['filter_round_domain'])){ $data['filter_round_domain'] = 0; }
	

	if(empty($data['link_param_start'])){ $data['link_param_start'] = ''; }
	if(empty($data['link_param_stop'])){ $data['link_param_stop'] = ''; }
	if(empty($data['filter_link_depth'])){ $data['filter_link_depth'] = ''; }
	if(empty($data['filter_link_slash'])){ $data['filter_link_slash'] = 0; }
	if(empty($data['filter_link_domain'])){ $data['filter_link_domain'] = 0; }

	//собираем параметры парсинга в ссылках
	if(!empty($data['round_param_start']) || !empty($data['round_param_stop'])){
		$filter_round_param = $data['round_param_start'].'{!na!}'.$data['round_param_stop'];
	}else{
		$filter_round_param = '';
	}

	if(!empty($data['link_param_start']) || !empty($data['link_param_stop'])){
		$filter_link_param = $data['link_param_start'].'{!na!}'.$data['link_param_stop'];
	}else{
		$filter_link_param = '';
	}

	$this->db->query("UPDATE `" . DB_PREFIX . "pars_setting` SET
		`dn_name`='".$this->db->escape($data['dn_name'])."',
		`start_link`='".$this->db->escape($data['start_link'])."',
		`page_cou_link`='".(int)$data['page_cou_link']."',
		`filter_round_yes`='".$this->db->escape($data['filter_round_yes'])."',
		`filter_round_no`='".$this->db->escape($data['filter_round_no'])."',
		`filter_round_method`='".$this->db->escape($data['filter_round_method'])."',
		`filter_round_param`='".$this->db->escape($filter_round_param)."',
		`filter_round_depth`='".$this->db->escape($data['filter_round_depth'])."',
		`filter_round_slash`='".$this->db->escape($data['filter_round_slash'])."',
		`filter_round_domain`='".$this->db->escape($data['filter_round_domain'])."',
		`filter_link_yes`='".$this->db->escape($data['filter_link_yes'])."',
		`filter_link_no`='".$this->db->escape($data['filter_link_no'])."',
		`filter_link_method`='".$data['filter_link_method']."',
		`filter_link_param`='".$this->db->escape($filter_link_param)."',
		`filter_link_depth`='".$this->db->escape($data['filter_link_depth'])."',
		`filter_link_slash`='".$this->db->escape($data['filter_link_slash'])."',
		`filter_link_domain`='".$this->db->escape($data['filter_link_domain'])."',
		`type_grab`='".$this->db->escape($data['type_grab'])."',
		`thread`='".$this->db->escape($data['thread'])."',
		`pars_pause`='".$this->db->escape($data['pars_pause'])."'
		WHERE `dn_id`=".(int)$dn_id);

	//настройки браузера.
  $this->db->query("UPDATE `".DB_PREFIX."pars_browser` SET cache_page = ".(int)$data['cache_page']." WHERE dn_id =".(int)$dn_id);

}

//Входная фунция на парсинг.
public function grabControl($i, $dn_id){
	//Получаем настройки
	$setting = $this->getSetting($dn_id);
	$links = $this->db->query("SELECT * FROM " . DB_PREFIX . "pars_sen_link WHERE scan = 1 AND `dn_id`=".(int)$dn_id." LIMIT 0,50");
	#$this->wtfarrey($links);
	$ans = [];
	if($links->num_rows > 0){
		//Блак многопоточности. берем нужное количество ссылок.
		$urls = [];
		foreach($links->rows as $key => $url){
			if($key < $setting['thread']){ $urls[] = $url['link']; } else { break; }
		}
		#$this->wtfarrey($urls);
		//делаем мульти запрос
		$datas = $this->multiCurl($urls, $dn_id);
		#$this->wtfarrey($datas);
		//Обрабатываем данные с мульти запроса. 
		foreach($datas as $key => $data){
				    	
    	//производим зяпись лога курл, и паролельно проверяем нужно ли делать дальнейшую работу.
	  	$curl_error = $this->sentLogMultiCurl($data ,$dn_id);
	  	
	  	#помечаем ссылку как отсканированная
    	$this->db->query("UPDATE ".DB_PREFIX."pars_sen_link SET scan=0 WHERE link='".$this->db->escape($data['url'])."' AND dn_id=".$dn_id);

	  	//если пришла ошибка заканчиваем эту итерацию и переходим к следующей.
	  	if($curl_error['error']){ 
  			continue;
  		}
	  		
			//передаем на обработку данные
			$this->ParsLink($data, $setting, $dn_id);
		}

		//Получаем количество ссылок для показа. Ненужная нагрузка но всем нравится.
		$ans['sen_count'] = $this->db->query("SELECT COUNT(*) as sen_count FROM " . DB_PREFIX . "pars_sen_link WHERE scan = 0 AND `dn_id`=".(int)$dn_id);
		$ans['sen_count'] = $ans['sen_count']->row['sen_count'];

		$ans['sen_count_no'] = $this->db->query("SELECT COUNT(*) as sen_count_no FROM " . DB_PREFIX . "pars_sen_link WHERE scan = 1 AND `dn_id`=".(int)$dn_id);
		$ans['sen_count_no'] = $ans['sen_count_no']->row['sen_count_no'];

		$ans['link_count'] = $this->db->query("SELECT COUNT(*) as link_count FROM " . DB_PREFIX . "pars_link WHERE `dn_id`=".(int)$dn_id);
		$ans['link_count'] = $ans['link_count']->row['link_count'];
		#$this->wtfarrey($ans);
		#пауза парсинга
    $this->timeSleep($setting['pars_pause']);

		$this->answjs('go', 'Производится сбор ссылок', $ans);

	}else{

		//Проверяем итерацию это начала парсинга или нет.
		if($i == 1){

			//Блак многопоточности. берем нужное количество ссылок.
			$urls[] = $setting['start_link'];

			//делаем мульти запрос
			$datas = $this->multiCurl($urls, $dn_id);

			//Обрабатываем данные с мульти запроса. 
			foreach($datas as $key => $data){
				#помечаем ссылку как отсканированная
	    	$this->db->query("UPDATE ".DB_PREFIX."pars_sen_link SET scan=0 WHERE link='".$data['url']."' AND dn_id=".$dn_id);

	    	//производим зяпись лога курл, и паролельно проверяем нужно ли делать дальнейшую работу.
	  		$curl_error = $this->sentLogMultiCurl($data ,$dn_id);
	  		//если пришла ошибка заканчиваем эту итерацию и переходим к следующей.
	  		if($curl_error['error']){ 
  				continue;
  			}

				//передаем на обработку данные
				$this->ParsLink($data, $setting, $dn_id);
			}

			//Получаем количество ссылок для показа. Ненужная нагрузка но всем нравится.
			$ans['sen_count'] = $this->db->query("SELECT COUNT(*) as sen_count FROM " . DB_PREFIX . "pars_sen_link WHERE scan = 0 AND `dn_id`=".(int)$dn_id);
			$ans['sen_count'] = $ans['sen_count']->row['sen_count'];

			$ans['sen_count_no'] = $this->db->query("SELECT COUNT(*) as sen_count_no FROM " . DB_PREFIX . "pars_sen_link WHERE scan = 1 AND `dn_id`=".(int)$dn_id);
			$ans['sen_count_no'] = $ans['sen_count_no']->row['sen_count_no'];

			$ans['link_count'] = $this->db->query("SELECT COUNT(*) as link_count FROM " . DB_PREFIX . "pars_link WHERE `dn_id`=".(int)$dn_id);
			$ans['link_count'] = $ans['link_count']->row['link_count'];
			#$this->wtfarrey($ans);
			#пауза парсинга
    	$this->timeSleep($setting['pars_pause']);
			$this->answjs('go', 'Производится сбор ссылок', $ans);

		}else{
			$this->answjs('link_end','Сбор ссылок завершен.');
		}

	}
}

//Основня фунция парсинга ссылок
public function ParsLink($data, $setting, $dn_id){

	//определяем тип сбора ссылок.
	if ($setting['type_grab'] == 1) {
		$reg_url = '#<a.+?href=["\']?([^"\'>]+)["\']?#s';
	}else{
		$reg_url = '#\<loc\>(.*?)\<\/loc\>#s';
		#$reg_url = '#<url>(.*?)</url>#s';

	}

	//передаем нужные данные.
	$url = $data['url'];
	$pre_html = $data['content'];

	#$this->wtfarrey($pre_html);
	$who = 'all'; #Тип фильтра по умолчанию.

	//Проверяем для кого фильтруем. Если нет области внутри которой искать ссылки, тогда для всех
	//all - для двух списков; filter_round_param - для очереди; filter_link_param - для выдачи
	//
	//Если пусто то all
	if (empty($setting['filter_round_param'].$setting['filter_link_param'])) {

		//Делим парсинг ссылок на отдельные списки
		preg_match_all($reg_url, $pre_html, $data_links);
		#$this->wtfarrey($data_links);

		if(!empty($data_links)){
			$data_link = $this->madeTidyLinks($data_links, $url);
			$this->filterLink($data_link, $setting, $dn_id, $who);
		}

	} else {
		//Есть ограничения по области.

		//
		//Для очереди.
		//
		$who = 'filter_round';
		//Проверяем если параметры париснга
		if(!empty($setting['filter_round_param'])){

			//Получаем параметры париснга очереди.
			$param_raund = explode('{!na!}', $setting['filter_round_param']);
			
			//Если нет одной из границ добавляем начало либо конец строки.
			if(empty($param_raund[0])){ $param_raund[0] = '^'; } else { $param_raund[0] = preg_quote(htmlspecialchars_decode($param_raund[0]), '#');}
			if(empty($param_raund[1])){ $param_raund[1] = '$'; } else { $param_raund[1] = preg_quote(htmlspecialchars_decode($param_raund[1]), '#');}

			//выполняе запрос на получение куска кода
			$reg = '#'. $param_raund[0] .'(.*?)'. $param_raund[1] .'#su';
			preg_match($reg, $pre_html, $html);

			//проверяем что бы был хоть какой то код.
			if (!empty($html[1])) {
				//выполняем сбор ссылок внутри вырезанного кода.
				preg_match_all($reg_url, $html[1], $data_links);
				#$this->wtfarrey($data_links);
				if(!empty($data_links)){
					//Приводим в порядок ссылки
					$data_link = $this->madeTidyLinks($data_links, $url);

					//отправляем на фильтрацию для очереди сканирования.
					$this->filterLink($data_link, $setting, $dn_id, $who);
				}
			}

		}else{

			preg_match_all($reg_url, $pre_html, $data_links);
			if(!empty($data_links)){
				//Приводим в порядок ссылки
				$data_link = $this->madeTidyLinks($data_links, $url);

				//отправляем на фильтрацию для очереди сканирования.
				$this->filterLink($data_link, $setting, $dn_id, $who);
			}
		}

		//
		//Для выдачи.
		//
		$who = 'filter_link';
		unset($html);
		//Проверяем если параметры париснга
		if(!empty($setting['filter_link_param'])){

			//Получаем параметры париснга выдачи
			$param_link = explode('{!na!}', $setting['filter_link_param']);

			//Если нет одной из границ добавляем начало либо конец строки.
			if(empty($param_link[0])){ $param_link[0] = '^'; } else { $param_link[0] = preg_quote(htmlspecialchars_decode($param_link[0]), '#');}
			if(empty($param_link[1])){ $param_link[1] = '$'; } else { $param_link[1] = preg_quote(htmlspecialchars_decode($param_link[1]), '#');}

			//выполняе запрос на получение куска кода
			$reg = '#'. $param_link[0] .'(.*?)'. $param_link[1] .'#su';
			preg_match($reg, $pre_html, $html);
			
			//проверяем что бы был хоть какой то код.
			if (!empty($html[1])) {
				//выполняем сбор ссылок внутри вырезанного кода.
				preg_match_all($reg_url, $html[1], $data_links);

				if(!empty($data_links)){
					//Приводим в порядок ссылки
					$data_link = $this->madeTidyLinks($data_links, $url);

					//отправляем на фильтрацию для очереди сканирования.
					$this->filterLink($data_link, $setting, $dn_id, $who);
				}
			}

		}else{

			preg_match_all($reg_url, $pre_html, $data_links);
			if(!empty($data_links)){
				//Приводим в порядок ссылки
				$data_link = $this->madeTidyLinks($data_links, $url);

				//отправляем на фильтрацию для очереди сканирования.
				$this->filterLink($data_link, $setting, $dn_id, $who);
			}
		}

	}

}

//фунция загрузки ссылок с файла.
public function uploadLinkFromFile($data, $dn_id, $who=2){
	//Перебираем файл и составляем массив.
	$links_file = explode(PHP_EOL, $data);

	//перебираем массив оставляем только ссылки.
	$links = [];
	foreach ($links_file as $key => $value) {
		if(preg_match('#^http#', $value)){ $links[] = $value; }
	}

	#Если в файле нету ссылок то завершаем работу. 
	if(empty($links)){
		$this->session->data['error'] = ' В файле нету ссылок для добавления.';
		return;
	}

	//получаем настройки и отправляем ссылки на запись в базу.
	$setting = $this->getSetting($dn_id);
	if ($who == 1) { $who = 'filter_link';} else { $who = 'filter_round'; }
	$this->filterLink($links, $setting, $dn_id, $who);
	$this->session->data['success'] = ' Все ссылки что СООТВЕТСТВОВАЛИ ВАШИМ НАСТРОЙКАМ были добавлены в список.';

	#$this->wtfarrey($links);
}

//Фильтрация ссылок.
//ВНИМАНИЕ !!! ссылки должны приходить полные c http://
public function filterLink($links, $setting, $dn_id, $who = 'all'){

	if ($who == 'all' || $who == 'filter_round') {
		$filter_round_yes = preg_split('#\n|\r\n|\r#', $setting['filter_round_yes']);
		$filter_round_no = preg_split('#\n|\r\n|\r#', $setting['filter_round_no']);
		$filter_round_method = $setting['filter_round_method'];
		$link_round = [];
	}

	if ($who == 'all' || $who == 'filter_link') {
		$filter_link_yes = preg_split('#\n|\r\n|\r#', $setting['filter_link_yes']);
		$filter_link_no = preg_split('#\n|\r\n|\r#', $setting['filter_link_no']);
		$filter_link_method = $setting['filter_link_method'];
		$link_finish = [];
	}


	//приводим в порядок фильтры.
	if ($who == 'all' || $who == 'filter_round') {

		foreach ($filter_round_yes as $key => $value) {
			if(!empty(trim($value))){
				$filter_round_yes[$key] = $this->modFilterLinkRules($value);
			}else{
				unset($filter_round_yes[$key]);
			}
		}

		foreach ($filter_round_no as $key => $value) {
			if(!empty(trim($value))){
				$filter_round_no[$key] = $this->modFilterLinkRules($value);;
			}else{
				unset($filter_round_no[$key]);
			}
		}

	}

	if ($who == 'all' || $who == 'filter_link') {
		
		foreach ($filter_link_yes as $key => $value) {
			if(!empty(trim($value))){
				$filter_link_yes[$key] = $this->modFilterLinkRules($value);
			}else{
				unset($filter_link_yes[$key]);
			}
		}

		foreach ($filter_link_no as $key => $value) {
			if(!empty(trim($value))){
				$filter_link_no[$key] = $this->modFilterLinkRules($value);
			}else{
				unset($filter_link_no[$key]);
			}
		}

	}

	//
	//Производим фильтрацию.
	//
	if(!empty($links)){

		// Формирую значение для проверки домена. проверяем нужно ли делать проверку доменного имени, если да то подготавливаемдомен
		if (($setting['filter_round_domain'] || $setting['filter_link_domain']) && !empty($setting['start_link'])) {
			$domain = parse_url($setting['start_link']);
			//Специально подготавливаю домен что бы определить по нему внутренная или внешнаяя ссылка. Без регулярных выражений
			$domain['host'] = '//'.$domain['host'];
			#$this->wtfarrey($domain);
		}

		foreach($links as $link){ 
			$link = htmlspecialchars_decode($link);

			//////////////////////////////////////////////
			//Для очереди
			//////////////////////////////////////////////

			if ($who == 'all' || $who == 'filter_round') {
				$permit = 1; #допуск к выполнению фильтров.
				//Пороверяем что делать с слешем
				// 0 - не важно
				// 1 - слеш в конце ссылки
				// 2 - только без слеша
				if ($setting['filter_round_slash'] == 1 && substr($link, -1) != "/") { $permit = 0; }
				if ($setting['filter_round_slash'] == 2 && substr($link, -1) == "/") { $permit = 0; }

				//Уровень вложенности
				if (!empty($setting['filter_round_depth'])) {

					//получаем уровни вложенности в ссылке
					$link_depth = count(array_diff(explode("/", $link), array(''))) - 1;
					//Получаем параметры вложенности.
					$depth = explode('-', $setting['filter_round_depth']);

					if (!isset($depth[1]) || ((int)$depth[1] == 0)) {
						//Это не диапазон
						$depth[0] = (int)$depth[0];
						//основная проверка
						if ($link_depth != $depth[0]) { $permit = 0;}

					}else{

						//Диапазон
						$depth[0] = (int)$depth[0];
						$depth[1] = (int)$depth[1];
						//основная проверка
						if ($link_depth < $depth[0] || $link_depth > $depth[1]) { $permit = 0;}
					}

				}

				//Проверяем доменное имя.
				// 0- Внутренние и внешние ссылки
        // 1 - Только внутренние ссылки
        // 2 - Только внешние ссылки
				if ($setting['filter_round_domain'] == 1 && !empty($setting['start_link'])){ 

					if(stripos($link, $domain['host']) === false){ $permit = 0; }

				}elseif ($setting['filter_round_domain'] == 2){ 

					if(stripos($link, $domain['host'])){ $permit = 0; }

				}

				#$this->wtfarrey($permit);
				if ($permit) {
					//Проверяем есть ли фильтры
					if(!empty($filter_round_yes)){

						if ($filter_round_method == 'or') {

							foreach ($filter_round_yes as $filter) {
								#$reg = '#'.preg_quote($filter, '#').'#';
								if(preg_match($filter, $link)){
									$go_round = $link;
									break;
								}
							}

						} elseif ($filter_round_method == 'and') {

							$link_tmp = $link;
							foreach ($filter_round_yes as $filter) {
								#$reg = '#'.preg_quote($filter, '#').'#';
								if(!preg_match($filter, $link_tmp)){
									unset($link_tmp);
									break;
								}
							}
							//Если все фильтры совпали записуем в массив
							if(!empty($link_tmp)) { $go_round = $link_tmp; }

						}

	  			} else {
	  				$go_round = $link;
	  			}

  			}

			}

			//////////////////////////////////////////////
			//Для выдачи
			/////////////////////////////////////////////

			if ($who == 'all' || $who == 'filter_link') {
				$permit = 1; #допуск к выполнению фильтров.
				//Пороверяем что делать с слешем
				// 0 - не важно
				// 1 - слеш в конце ссылки
				// 2 - только без слеша
				if ($setting['filter_link_slash'] == 1 && substr($link, -1) != "/") { $permit = 0; }
				if ($setting['filter_link_slash'] == 2 && substr($link, -1) == "/") { $permit = 0; }

				//Уровень вложенности
				if (!empty($setting['filter_link_depth'])) {

					//получаем уровни вложенности в ссылке
					$link_depth = count(array_diff(explode("/", $link), array(''))) - 1;
					//Получаем параметры вложенности.
					$depth = explode('-', $setting['filter_link_depth']);

					if (!isset($depth[1]) || ((int)$depth[1] == 0)) {
						//Это не диапазон
						$depth[0] = (int)$depth[0];
						//основная проверка
						if ($link_depth != $depth[0]) { $permit = 0;}

					}else{
						
						//Диапазон
						$depth[0] = (int)$depth[0];
						$depth[1] = (int)$depth[1];
						//основная проверка
						if ($link_depth < $depth[0] || $link_depth > $depth[1]) { $permit = 0;}
					}

				}

				//Проверяем доменное имя.
				// 0- Внутренние и внешние ссылки
        // 1 - Только внутренние ссылки
        // 2 - Только внешние ссылки
				if ($setting['filter_link_domain'] == 1 && !empty($setting['start_link'])){
					#$this->wtfarrey('Вариант 1 |'.$link.' | '.$domain['host']); 
					if(stripos($link, $domain['host']) === false){ $permit = 0; }
				}elseif ($setting['filter_link_domain'] == 2 && !empty($setting['start_link'])){ 
					if(stripos($link, $domain['host'])){ $permit = 0; }
				}

				if ($permit) {
					//если фильтры не пустые
	  			if(!empty($filter_link_yes)){
						if ($filter_link_method == 'or') {

							foreach ($filter_link_yes as $filter) {
								#$reg = '#'.preg_quote($filter, '#').'#';
								if(preg_match($filter, $link)){
									$go_finish = $link;
									break;
								}
							}

						} elseif ($filter_link_method == 'and') {

							$link_tmp = $link;
							foreach ($filter_link_yes as $filter) {
								#$reg = '#'.preg_quote($filter, '#').'#';
								if(!preg_match($filter, $link_tmp)){
									unset($link_tmp);
									break;
								}
							}
							//Если все фильтры совпали записуем в массив
							if(!empty($link_tmp)) { $go_finish = $link_tmp; }

						}

					} else {
						$go_finish = $link;
					}
				}
			}

			//Конец положительных фильтров
			//Начало фильтров отрицания.

			///////////////////////////////////////////////////////
			//Очередь
			///////////////////////////////////////////////////////

			if ($who == 'all' || $who == 'filter_round') {
				//Проверяем есть ли ссылки вообше
				if (!empty($go_round)) {

					$link_temp = $go_round;
					if (!empty($filter_round_no)) {
						#$this->wtfarrey($filter_round_no);
						//производим проверку
						foreach ($filter_round_no as $filter) {
							#$reg = '#'.preg_quote($filter, '#').'#';
							if(preg_match($filter, $link_temp)){
								unset($link_temp);
								break;
							}
						}

						if (!empty($link_temp)) { $link_round[] = $link_temp; }

					} else {
						$link_round[] = $link_temp;
					}
				}
			}
			///////////////////////////////////////////////////////
			//Выдача
			///////////////////////////////////////////////////////

			if ($who == 'all' || $who == 'filter_link') {
				//Проверяем есть ли ссылки вообше
				if (!empty($go_finish)) {
					$link_temp = $go_finish;
					if (!empty($filter_link_no)) {
						//производим проверку
						foreach ($filter_link_no as $filter) {
							#  '#'.$filter.'#';
							if(preg_match($filter, $link_temp)){
								unset($link_temp);
								break;
							}
						}

						if (!empty($link_temp)) { $link_finish[] = $link_temp; }

					} else {
						$link_finish[] = $link_temp;
					}
				}
			}
			//В конце цикла фильтрации удаляем.
			unset($go_round);
			unset($go_finish);
		}

	}

	//Для повторной фильтрации проверка.
	if ($who == 'all') {

		if (!empty($link_round)) {
			foreach ($link_round as $round) {
				$this->AddParsSenLink($round, $dn_id);
			}
		}

		if (!empty($link_finish)) {
			foreach ($link_finish as $finish) {
				$this->AddParsLink($finish, $dn_id);
			}
		}

	}elseif($who == 'filter_round'){

		if (!empty($link_round)) {
			foreach ($link_round as $round) {
				$this->AddParsSenLink($round, $dn_id);
			}
		}

	} elseif ($who == 'filter_link') {

		if (!empty($link_finish)) {
			foreach ($link_finish as $finish) {
				$this->AddParsLink($finish, $dn_id);
			}
		}

	}
	#$this->wtfarrey($link_round);
	#$this->wtfarrey($link_finish);
}

//приводим ссылки в правильный вид, убераем ненужное, добавляем нужное.
public function madeTidyLinks($data_link, $url){
	//получаем главный домен, для работы.
	$url = parse_url($url);
	$domain = $url['scheme'].'://'.$url['host'];

	#Убираем дубли
	$data_link = array_unique($data_link[1]);

	foreach($data_link as $key => $value){

		if(!empty($value)){
			//преобразуем сушности в символы, если на сайте доноре используют сушности по типу &#x2F;
			$value = html_entity_decode($value);
			//Фикс относительных ссылок. удаляем ../ в ссылках
			$value = str_replace('../', '/', $value);
			//Удаляем ненужные переносы строки и пробелы.
			$value = trim(str_replace(PHP_EOL, '', $value));

			//боримся с доменами где ссылка начинается на //
			$value = preg_replace('#^\/\/#', $url['scheme'].'://', $value);
			$http = parse_url($value);
			if(empty($http['scheme']) or empty($http['host'])){

				if($value[0] != '/'){
					$data_link[$key] = $domain.'/'.$value;
				}else{
					$data_link[$key] = $domain.$value;
				}

			}else{
				$data_link[$key] = $value;
			}
		}else{
			unset($data_link[$key]);
		}
	}

	return $data_link;
}

//Фунция для модификации правил поиска, для использования {skip}
public function modFilterLinkRules($filter) {
	$value = '';
	//Если фильтр не пустой начинаем зачишать. И преобразовывать.
	if (!empty($filter)) {

		//Отлавливаем регулярные вырежения в правилах поиск замена
		if(preg_match('#^\{reg\[(.*)\]\}$#', $filter, $reg)){
			#$this->wtfarrey($reg);
			$value = htmlspecialchars_decode($reg[1]);

		}else{

			$value = '#' . str_replace('\{skip\}', '(.*?)', preg_quote(trim(htmlspecialchars_decode($filter)), '#')) . '#';

		}

	}
	#$this->wtfarrey($value);
	return $value;
}
############################################################################################
############################################################################################
#						Фунции страницы ParamSetup
############################################################################################
############################################################################################

#Вывод формирования страницы Paramsetup
public function GetParamsetup($dn_id){
	//Получаем настройки.

	$data_setting = $this->db->query("SELECT * FROM `". DB_PREFIX ."pars_setting` WHERE dn_id=".(int)$dn_id);
	$data['setting'] = $data_setting->row;
	#$this->wtfarrey($data['setting']);
	#Получаем ссылки
	$data_links = $this->db->query("SELECT * FROM `". DB_PREFIX ."pars_link` WHERE dn_id=".(int)$dn_id." ORDER BY id ASC LIMIT 0, 100");
	$data['hrefs'] = $data_links->rows;

	#Получаем список параметров.
	$data_params = $this->db->query("SELECT * FROM `". DB_PREFIX ."pars_param` WHERE dn_id=".(int)$dn_id." ORDER BY type, id ASC");
	$data['params'] = $data_params->rows;

	$browser = $this->db->query("SELECT * FROM `". DB_PREFIX ."pars_browser` WHERE dn_id=".(int)$dn_id);
	$data['browser'] = $browser->row;
	#$this->wtfarrey($data);

	return $data;
}

#Получение параметра который редактируем
public function getActivParam($data){
	$data_param_activ = $this->db->query("SELECT * FROM `". DB_PREFIX ."pars_param` WHERE id=".(int)$data);
	$param_activ = [];
	foreach ($data_param_activ->row as $key => $value) {
		$param_activ[$key] = htmlspecialchars_decode($value);
	}
	#$this->wtfarrey($param_activ);
	return $param_activ;
}

#Сохранения параметра парсинга
public function addParamPars($data, $dn_id){
	#$this->wtfarrey($data);
	if($data['type_param'] == 1 or $data['type_param'] == 3){
		$data['delim'] = ';';
		$data['base_id'] = 0;
		$data['reverse'] = 0;
	}

	$this->db->query("INSERT INTO " . DB_PREFIX . "pars_param SET
		dn_id =".(int)$dn_id.",
		name='".$this->db->escape($data['param_name'])."',
		start='".$this->db->escape($data['param_start'])."',
		stop='".$this->db->escape($data['param_stop'])."',
		type=".(int)$data['type_param'].",
		with_teg=".(int)$data['with_teg'].",
		skip_enter='".$this->db->escape($data['skip_enter'])."',
		skip_where=".(int)$data['skip_where'].",
		reverse=".(int)$data['reverse'].",
		base_id=".(int)$data['base_id'].",
		delim='".$this->db->escape($data['delim'])."'");

		$param = $this->getActivParam($this->db->getLastId());

		return $param;
}

#Обновление параметра парсинга
public function saveParamPars($data){
	#$this->wtfarrey($data);

	if($data['type_param'] == 1){
		$data['delim'] = ';';
		$data['base_id'] = 0;
		$data['reverse'] = 0;
	}

	$this->db->query("UPDATE `". DB_PREFIX ."pars_param` SET
		`name`='".$data['param_name']."',
		`start`='".$this->db->escape($data['param_start'])."' ,
		`stop`='".$this->db->escape($data['param_stop'])."',
		`type`='".$this->db->escape($data['type_param'])."',
		`with_teg`=".(int)$data['with_teg'].",
		`skip_enter`='".$this->db->escape($data['skip_enter'])."',
		`skip_where`=".(int)$data['skip_where'].",
		`reverse`=".(int)$data['reverse'].",
		`base_id`=".(int)$data['base_id'].",
		`delim`='".$this->db->escape($data['delim'])."'
		WHERE `id`=".(int)$data['act']);
}

public function delParamPars($id){
	$this->db->query("DELETE FROM `". DB_PREFIX ."pars_param` WHERE id=".(int)$id);
	//Удаляем значения поиск замена для удаленного параметра парсинга.
	$this->db->query("DELETE FROM `". DB_PREFIX ."pars_replace` WHERE param_id=".(int)$id);

	//Удаление файлов кеша, поиск замены.
	$file = DIR_APPLICATION.'simplepars/replace/'.$id;
	//Проверяем есть ли такой фаил
	if (file_exists($file.'_input_arr.txt')) { unlink($file.'_input_arr.txt'); }
	if (file_exists($file.'_input_text.txt')) { unlink($file.'_input_text.txt'); }
	if (file_exists($file.'_output.txt')) { unlink($file.'_output.txt'); }
}

//Скрыть оnбразить пред просмотр сайта.
public function setViewParam($data, $dn_id){
	$this->db->query("UPDATE `". DB_PREFIX ."pars_setting` SET pre_view_param=".(int)$data['pre_view_param']." WHERE dn_id=".(int)$dn_id);
	$this->db->query("UPDATE `". DB_PREFIX ."pars_setting` SET pre_view_syntax=".(int)$data['pre_view_syntax']." WHERE dn_id=".(int)$dn_id);
}

//Изменить параметр использования кеша.
public function changeCacheParam($data, $dn_id){
	$this->db->query("UPDATE `".DB_PREFIX."pars_browser` SET cache_page = ".(int)$data['cache_page']." WHERE dn_id =".(int)$dn_id);
}
//Показать часть вырезанного кода.
public function showPieceCode($data, $dn_id){
	//параметры что предпросматриваем.
	$html = $this->CachePage($data['link'],$dn_id);
	//Обычная граница
	if($data['type_param']==1){

		$start = htmlspecialchars_decode($data['param_start']);
		$stop = htmlspecialchars_decode($data['param_stop']);

		$reg = '#'. preg_quote($start, '#').'(.*?)'.preg_quote($stop, '#') .'#su';

		preg_match_all($reg, $html, $pre_view);

		$pre_view[$data['with_teg']] = $this->skipEntryParam($pre_view[$data['with_teg']], 1, $data['skip_where'], $data['skip_enter']);

		if(empty($pre_view[$data['with_teg']][0])){$pre_view[$data['with_teg']][0]='';}
		$return['activ_param']['type'] = 1;
		$return['page_code'] = $pre_view[$data['with_teg']][0];
		$return['activ_param']['name'] = $data['param_name'];
		$return['activ_param']['id'] = $data['param_id'];
		$return['activ_param']['start'] = htmlspecialchars($start);
		$return['activ_param']['stop'] = htmlspecialchars($stop);
		$return['activ_param']['with_teg'] = $data['with_teg'];
		$return['activ_param']['skip_enter'] = $data['skip_enter'];
		$return['activ_param']['skip_where'] = $data['skip_where'];

	//Повторяющаяя граница парсинаг
	}elseif($data['type_param']==2){
		if($data['base_id'] != 0){
			//Получаем информацию о границах парсинга.
			$param_base = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_param WHERE id=".(int)$data['base_id']);
			$param_base = $param_base->row;
 			$start_base = htmlspecialchars_decode($param_base['start']);
	   	$stop_base = htmlspecialchars_decode($param_base['stop']);
	   	$reg = '#'. preg_quote($start_base, '#').'(.*?)'.preg_quote($stop_base, '#') .'#su';
	  	preg_match_all($reg, $html, $code);
	  	//Опередяем пропуск вхождения
  		$code[$param_base['with_teg']] = $this->skipEntryParam($code[$param_base['with_teg']], 1, $param_base['skip_where'], $param_base['skip_enter']);
  	}else{
  		$code[0][0] = $html;
  		$param_base['with_teg'] = 0;
  	}


  	//получили границу парсинга, если ее нет добавили пробел.
  	if(empty($code[$param_base['with_teg']][0])){$code[$param_base['with_teg']][0]='';}

  	//проверяем задал ли пользователь границы парсинга, если нет отдаем ему то что после обычных границ
  	if(empty($data['param_start']) || empty($data['param_stop'])){

  		$return['page_code'] = $code[$param_base['with_teg']][0];
  		$return['activ_param']['name'] = $data['param_name'];
  		$return['activ_param']['id'] = $data['param_id'];
  		$return['activ_param']['start'] = '';
			$return['activ_param']['stop'] = '';
			$return['activ_param']['type'] = 2;
			$return['activ_param']['with_teg'] = $data['with_teg'];
			$return['activ_param']['skip_enter'] = $data['skip_enter'];
			$return['activ_param']['skip_where'] = $data['skip_where'];
			$return['activ_param']['delim'] = $data['delim'];
			$return['activ_param']['reverse'] = $data['reverse'];
			$return['activ_param']['base_id'] = $data['base_id'];

  	}elseif(!empty($data['param_start']) && !empty($data['param_stop'])){
	  	//начал парсинга повторяющей границы.
	  	$start = htmlspecialchars_decode($data['param_start']);
			$stop = htmlspecialchars_decode($data['param_stop']);

			$reg = '#'. preg_quote($start, '#').'(.*?)'.preg_quote($stop, '#') .'#su';

			preg_match_all($reg, $code[$param_base['with_teg']][0], $pre_view);

			//Отсееваем ненужные вхождения
			$pre_view[$data['with_teg']] = $this->skipEntryParam($pre_view[$data['with_teg']], 2, $data['skip_where'], $data['skip_enter']);
			//Вывод массива в обратном порядке.
			if($data['reverse'] == 1){
				$pre_view[$data['with_teg']] = array_reverse($pre_view[$data['with_teg']]);
			}

			$return['activ_param']['type'] = 2;
			$i = 1;
			$return['page_code'] ='';
			foreach($pre_view[$data['with_teg']] as $text){
				$return['page_code'] .= '!=========================================================== Повторение №'.$i.' ========== Разделитель ['.$data['delim'].'] =================================================!'.PHP_EOL.PHP_EOL.$text.PHP_EOL.PHP_EOL;
				$i++;
			}
			$return['activ_param']['name'] = $data['param_name'];
			$return['activ_param']['id'] = $data['param_id'];
			$return['activ_param']['start'] = htmlspecialchars($start);
			$return['activ_param']['stop'] = htmlspecialchars($stop);
			$return['activ_param']['type'] = 1;
			$return['activ_param']['with_teg'] = $data['with_teg'];
			$return['activ_param']['skip_enter'] = $data['skip_enter'];
			$return['activ_param']['skip_where'] = $data['skip_where'];
			$return['activ_param']['delim'] = $data['delim'];
			$return['activ_param']['reverse'] = $data['reverse'];
			$return['activ_param']['base_id'] = $data['base_id'];
		}
		#$this->wtfarrey($return);
	}

	return $return;
}

############################################################################################
############################################################################################
#						Фунции связанные с страницей настройки CSV прайса
############################################################################################
############################################################################################

	#Фунция получения настроек csv
	public function getFormCsv($dn_id){
  //получаем все настройки
	$setting = $this->getSetting($dn_id);
	
  $formcsv = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_createcsv WHERE dn_id=".(int)$dn_id." ORDER BY id ASC");
  
  $links_select = $this->db->query("SELECT * FROM " . DB_PREFIX . "pars_link WHERE `dn_id`=".(int)$dn_id." AND scan=1 ORDER BY id ASC LIMIT 0, ".$setting['page_cou_link']);

	$browser = $this->db->query("SELECT * FROM `". DB_PREFIX ."pars_browser` WHERE dn_id=".(int)$dn_id);
	$data['browser'] = $browser->row;


	//преобразуем данные допуска границ для страницы в парсинге в ИМ
	$setup = $this->getPrSetup($dn_id);
	$data['setup']['grans_permit_list'] = $this->madeGransPermitListToArr($setup['grans_permit_list']);


  if(empty($setting['csv_name'])){
    $setting['csv_name'] = 'price-'.$dn_id;
  }
  $csv_file = './uploads/'.$setting['csv_name'].'.csv';

  if (file_exists($csv_file)) {
  	$data['csv_exists'] = true;
  } else {
  	$data['csv_exists'] = false;

  }

  //получаем информацию о списках.
  $data['link_lists'] = $this->getAllLinkList($dn_id);
  $data['link_errors'] = $this->getAllLinkError($dn_id);


  //получаем ссылки для вывода информации
  #$pars_url = $this->getUrlToPars($dn_id, $setting['link_list'], $setting['link_error']);
  #$this->wtfarrey($pars_url);

  $data['setting'] = $setting;
  #$data['links_prepare'] = $pars_url['queue'];
  $data['links_select'] = $links_select->rows;
  $data['formcsv'] = $formcsv->rows;
  #$data['link_scan_count'] = $pars_url['total'] - $pars_url['queue'];
  #$data['link_full'] = $pars_url['full'];

 	return $data;
	}

	public function saveFormCsv($data, $dn_id){
 	#$this->wtfarrey($data);
 	$data['csv_escape'] = '"'; //Этот параметр более неиспользуется, и ждет время на полное удаление из базы.
 	#заменяем пробелы на нижнее подчеркивание в названии прайса.
 	$data['csv_name'] = str_replace(' ', '_', $data['csv_name']);

 	//Сохраняем имя и паузу парсинга.
	  $this->db->query("UPDATE ". DB_PREFIX ."pars_setting  SET 
	  	csv_name='".$this->db->escape($data['csv_name'])."' , 
	  	pars_pause='".$this->db->escape($data['pars_pause'])."', 
	  	thread='".$this->db->escape($data['thread'])."', 
	  	grans_permit='".$this->db->escape($data['grans_permit'])."', 
	  	csv_delim='".$this->db->escape($data['csv_delim'])."', 
	  	csv_escape='".$this->db->escape($data['csv_escape'])."', 
	  	csv_charset=".(int)$data['csv_charset']." WHERE dn_id=".(int)$dn_id);

	  //перед сохранением удаляем все настройки из базы
  	$this->db->query("DELETE FROM ". DB_PREFIX ."pars_createcsv WHERE dn_id=".(int)$dn_id);

  if(!empty($data['csv'])){

   #записывам все параметры заново.
   foreach($data['csv'] as $column){
    $this->db->query("INSERT INTO ". DB_PREFIX ."pars_createcsv SET 
    	dn_id=".(int)$dn_id.", 
    	name='".$this->db->escape($column['name'])."',
    	value='".$this->db->escape($column['value'])."',
    	csv_column='".$this->db->escape($column['csv_column'])."'");
   }
  }

  //Преобразуем блок допуска границ.
	if(!empty($data['grans_permit_list'])){

		//перебере
		foreach($data['grans_permit_list'] as $gran_arr_key => &$gran_arr){

			//если в поле не настроена граница парсинга то такое правело не сохраняем.
			if(empty($gran_arr['gran'])){
				unset($data['grans_permit_list'][$gran_arr_key]);
			}else{
				$gran_arr = implode('{!na!}', $gran_arr);
			}

		}
		#$this->wtfarrey($data['grans_permit_list']);
		$data['grans_permit_list'] = implode('{next}', $data['grans_permit_list']);

	}else{

		$data['grans_permit_list'] = '';
	
	}

	//сохраняем допуски.
	$this->db->query("UPDATE ". DB_PREFIX ."pars_prsetup SET grans_permit_list='".$this->db->escape($data['grans_permit_list'])."' WHERE dn_id=".(int)$dn_id);

  //настройки браузера.
  $this->db->query("UPDATE `".DB_PREFIX."pars_browser` SET cache_page = ".(int)$data['cache_page']." WHERE dn_id =".(int)$dn_id);

  $this->session->data['success'] = "Настройки сохранены";
	}
	#Контролер на добавление новых ссылок на парсинг
public function controlAddLink($data, $dn_id, $mark='link'){

 	$links = explode(PHP_EOL, $data);

 	if($mark == 'link'){
	  $this->DelParsLink($dn_id);

	  //Использую бредовую фунцию нужно написать свою фунцию по валидации url.
	  foreach($links as $link){
	    if(!empty($link)){
	      $url = parse_url($link);

	      if(!empty($url['scheme']) && !empty($url['host'])){
	        $this->AddParsLink($link, $dn_id);
	      }
	    }
	  }

	}elseif($mark == 'link_sen'){
		$this->DelParsSenLink($dn_id);
		foreach($links as $link){
	    if(!empty($link)){
	      $url = parse_url($link);

	      if(!empty($url['scheme']) && !empty($url['host'])){
	        $this->AddParsSenLink($link, $dn_id);
	      }
	    }
	  }

	}
}

#Контроллер для пред просмотра CSV.
public function controlShowParsToCsv($url, $dn_id){
  #проверка какой предпросмотр вызывается
  $html = $this->CachePage($url, $dn_id);
	$csv = $this->changeDataToCsv($html, $url, $dn_id);

	if($csv === false){
		$this->session->data['error'] = ' Отсутствуют настройки файла CSV';
		return $csv = 'redirect';
	}

	//Получам дополнительные данные из настроек.
	$setting = $this->getSetting($dn_id);
	//текст предупреждения
	$csv['permit_grans_text'] = '';

	//Получаем разрешения на действия.
	if(!empty($setting['grans_permit'])){
		//для совместимости 
		$tmp_html['content'] = $html;
		$tmp_html['url'] = $url;
		$form = $this->preparinDataToStore($tmp_html, $dn_id);

		$form['permit_grans'] = $this->checkGransPermit($form, $setting, $dn_id);

		//проверяем допуски
		if( empty($form['permit_grans'][4]['permit']) ){
			$csv['permit_grans_text'] = 'ВНИМАНИЕ!!!<br> Страница не будет спарсена.<br>Поскольку:'.$form['permit_grans'][4]['log'];
		}

	}

	$csv['value'] = $this->transformCsv($csv['value']);
	//отправляем на обработку логики.
	#$csv['value'] = $this->madeLogicalMathem($csv['value'], $type='int');
	#$this->wtfarrey($csv);
	array_walk_recursive($csv['value'], array($this, 'htmlview'));
	#$this->wtfarrey($csv);
	return $csv;
}

//Фунция составления парсинга в csv
public function changeDataToCsv($html, $url, $dn_id){
	$form = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_createcsv WHERE dn_id=".(int)$dn_id." ORDER BY id ASC");
	if($form->num_rows ==0){
		return $form = false;
	}
	$form = $form->rows;
	$params = $this->getParsParam($dn_id);

	//Перебераем все поля товара.
	foreach($form as $mark => $pdata){

		//Преобразовываем записи из базы. Что бы код не был в виде сушности.
		$form[$mark]['value'] = htmlspecialchars_decode($pdata['value']);
		#$this->wtfarrey($pdata);
		//А здесь все границы парсинга что были созданы.
		foreach($params as $param){

			//если какая то граница совпала начинаем делать ветер.
			if(strpos($pdata['value'], '{gran_'.$param['id'].'}') !== false){

				//парсим границу.
				$gvar = $this->parsParam($html, $param['id']);

				//Если первый тип границы, не повторяющийся. Тогда замешаем значения в лоб.
				if($param['type'] == 1){
					//применяем поск замена.
					$gvar = $this->findReplace($gvar, $param['id']);
					$form[$mark]['value'] = str_replace('{gran_'.$param['id'].'}', $gvar, $form[$mark]['value']);
				}
				#$this->wtfarrey($form);
				//Если повторяющиеся границы парсинга тогда перебераем массивы и составляем строку из массива, и замешаем.
				if($param['type'] == 2){
					$str = '';
					foreach($gvar as $gstr){
					//применяем поск замена.
						$gstr = $this->findReplace($gstr, $param['id']);
						if(empty($str)){ $str .= $gstr; }else{ $str .= $param['delim'].$gstr; }
					}
					#записываем в строку результат.
					$form[$mark]['value'] = str_replace('{gran_'.$param['id'].'}', $str, $form[$mark]['value']);
				}

			}
		}

		//преобразуем дополнительные данные
		$form[$mark]['value'] = str_replace('{link}', $url, $form[$mark]['value']);

		//преобразуем в стандартизироанный массив.
		$csv['title'] = [];
		$csv['value'] = [];
		foreach($form as $key => $value){
			
			//форматирование колонок с указанием строго количества колонок под одну границу
			$count_column = substr_count($value['value'],'{csvnc}') +1; #определяем реальное количествно колонок в границе.
			$value['csv_column'] = (int)$value['csv_column'];

			if(!empty($value['csv_column']) && $count_column > $value['csv_column']){

  			$value['value'] = explode('{csvnc}', $value['value']);
  			$value['value'] = array_splice($value['value'], 0, $value['csv_column']);
  			$value['value'] = implode('{csvnc}', $value['value']);
  			$csv['value'][] = $value['value'];

			}elseif(!empty($value['csv_column']) && $count_column < $value['csv_column']){
				
				while($count_column < $value['csv_column']){

					$value['value'] .= '{csvnc}';
					$count_column++;
				}
				$csv['value'][] = $value['value'];

			}else{
  			$csv['value'][] = $value['value'];
			}

			$csv['title'][] = $value['name'];
			//дублируем имена колонок.
			while( ($value['csv_column'] - 1) > 0 ){
				$csv['title'][] = $value['name'];
				$value['csv_column']--;
			}
		}
	}

	return $csv;

}
//Преобразованя операторов {csvnс} и {csvnl}
public function transformCsv($data){
	//перебор массива. {csvnl}
	#$this->wtfarrey($data);
	$il = 0;
	foreach($data as $key => $csvnl){
		//если есть совпадение с оператором {csvnc}
		if(strripos($csvnl, '{csvnl}')){
			$csvnl_arrey = explode('{csvnl}', $csvnl);
			if(is_array($csvnl_arrey)){
				foreach($csvnl_arrey as $value){
					$csvnl_data[$il][] = $value;
					$il++;

				}
			}
		}else{
			//Убераем один лищний перенос строки.
			if($il !=0){
				$il2 = $il -1;
			}else{
				$il2 = $il;
			}
			$csvnl_data[$il2][] = $csvnl;
		}
	}

	#$this->wtfarrey($csvnl_data);
	//Теперь разбиваем массив на ячейки. {csvnc}
	foreach($csvnl_data as $nl_key => $arr_csvnc){
		//Раскрываем многомерный массив,
		foreach($arr_csvnc as $nc_key => $csvnc){
		#$this->wtfarrey($csvnc);
  		if(strripos($csvnc, '{csvnc}') !== false){
  			$csvnc = explode('{csvnc}', $csvnc);
  			foreach($csvnc as $new_colum){
					$new_data[$nl_key][] = $this->madeLogicalMathem($new_colum, 'str');
  			}

  		}else{
  			$new_data[$nl_key][] = $this->madeLogicalMathem($csvnc, 'str');
  		}

		}

	}

	$this->wtfarrey($new_data);
	return $new_data;
}

//Создание файла csv
public function createCsv($csv, $setting, $dn_id){
  #Записываем, или дозаписываем данные csv файла
	$csv_delim = htmlspecialchars_decode($setting['csv_delim']);
	#$csv_escape = htmlspecialchars_decode($setting['csv_escape']);
	$csv_escape = '"';

	//Кодировка файла.
	$tail = '//TRANSLIT';
	if($setting['csv_charset'] == 1){
		$csv_charset = 'WINDOWS-1251'.$tail;
	}elseif($setting['csv_charset'] == 2){
		$csv_charset = 'UTF-8'.$tail;
	}else{
		$csv_charset = 'WINDOWS-1251'.$tail;
	}



  #имя файла по умолчанию
  $path = "./uploads/price-".$dn_id.".csv";

  #имя файла по желанию
  if(!empty($setting['csv_name'])){
    $path = "./uploads/".iconv("UTF-8", "WINDOWS-1251", $setting['csv_name']).".csv";
  }

  if(!file_exists($path)){
  	foreach($csv['title'] as $kay => $title){
	    @$csv['title'][$kay] = htmlspecialchars_decode(trim(iconv("UTF-8", $csv_charset, $title)));
	  }
    #открываем фаил и записываем title
    $file = fopen($path, 'a+');
      fputcsv($file, $csv['title'], $csv_delim, $csv_escape);
    fclose($file);
  }

  foreach($csv['value'] as $csv_data){
	  #Меняем кодировку для файла csv
	  foreach($csv_data as $kay => $csv_var){
	    @$csv_data[$kay] = trim(iconv("UTF-8", $csv_charset, $csv_var));
	  }

	  #$this->wtfarrey($csv_data);

	  $file = fopen($path, 'a+');
	    fputcsv($file, $csv_data, $csv_delim, $csv_escape);
	  fclose($file);
	}
}

#контролер парсинга в CSV. Храни меня господь разобратся что тут написал! ;)
public function controlParsDataToCsv($dn_id){
  $setting = $this->getSetting($dn_id);
  #$this->wtfarrey($setting);

  $pars_url = $this->getUrlToPars($dn_id, $setting['link_list'], $setting['link_error']);
  #$this->wtfarrey($pars_url);
  #Если ссылок нету завершаем работу модуля.
  if(empty($pars_url['links'])){

    $answ['progress'] = 100;
    $answ['clink'] = ['link_scan_count' => $pars_url['total'], 'link_count' => $pars_url['queue'],];
    $this->answjs('finish','Парсинг закончился, ссылок больше нет﻿',$answ);

  }else{

  	//собираем массив ссылок для мульти запроса.
  	$urls = [];
  	foreach($pars_url['links'] as $key => $url){
  		if($key < $setting['thread']) {$urls[] = $url['link']; } else { break; }
  	}

  	#$this->wtfarrey($urls);
  	$datas = $this->multiCurl($urls, $dn_id);
  	#$this->wtfarrey($datas);
	

  	//Далее разбираем данные из мульти курла и делаем все нужные записи.
  	foreach($datas as $key => $data){

  		//производим зяпись лога курл, и паролельно проверяем нужно ли делать дальнейшую работу.
  		$curl_error = $this->sentLogMultiCurl($data ,$dn_id);

  		#помечаем ссылку как отсканированная
    	$this->db->query("UPDATE ".DB_PREFIX."pars_link SET scan=0, error='".$curl_error['http_code']."' WHERE link='".$data['url']."' AND dn_id=".$dn_id);

  		//если пришла ошибка заканчиваем эту итерацию и переходим к следующей.
  		if($curl_error['error']){ 
  			continue;
  		}

			//Получаем разрешения на действия.
			if(!empty($setting['grans_permit'])){
				//плохая практика но что поделать, дергаем данные парсинга в ИМ
				$form = $this->preparinDataToStore($data, $dn_id);
				$permit_grans = $this->checkGransPermit($form, $setting, $dn_id);
				#$this->wtfarrey($permit_grans);
				//проверяем массив допуска и сравниваем с выбранным действием. 
				if( empty($permit_grans[4]['permit'])){ 
					$this->log('NoGranPermit', $permit_grans[4]['log'], $dn_id);
					continue; 
				}
			}

  		$html = $data['content'];
  		$csv = [];
  		$csv = $this->changeDataToCsv($html, $data['url'], $dn_id);

  		//Умышленно создаем маячек что нужно остановить загрузку прайса.
  		$finish = 0;
  		if($csv === false){ 
  			$finish = 1; 
  		} else {
  			//преобразовывем данные для csv
  			$csv['value'] = $this->transformCsv($csv['value']);
  			//записываем данные в csv
  			$this->createCsv($csv, $setting, $dn_id);
  			
  		}
  	}

  	//Если настройки csv несделаны отдаем ошибку что форма не настроена.
  	//$finish может быть как 1 так 0 и даже не определена, проверять толко через empty
  	if(!empty($finish)){
  		$answ['progress'] = 100;
  		$this->answjs('finish',' Отсутствуют настройки файла CSV',$answ);
  	}
    
    #считаем процент для прогрес бара
    $scan = ($pars_url['total']-$pars_url['queue']);
    $progress = $scan/($pars_url['total']/100);
    $answ['progress'] = $progress;
    $answ['clink'] = [
                       'link_scan_count' => $scan,
                       'link_count' => $pars_url['queue'],
                      ];
    #пауза парсинга
    $this->timeSleep($setting['pars_pause']);
    $this->answjs('go','Производится парсинг',$answ);
    #exit(json_encode($answ));
  }
}

############################################################################################
############################################################################################
#						Фунции связанные с страницей настройки Парсинга в ИМ
############################################################################################
############################################################################################
	//Получение параметров парсинга для выбора.
public function getSettingToProduct($dn_id){

	//получаем все настройки
	$setting = $this->getSetting($dn_id);

	//Удаляем ненужно, а то мешает отладке, удалить потом этот блок.
	unset($setting['filter_round_yes']);
	unset($setting['filter_round_method']);
	unset($setting['filter_round_no']);
	unset($setting['filter_link_yes']);
	unset($setting['filter_link_no']);
	unset($setting['filter_link_method']);
	unset($setting['start_link']);
	unset($setting['csv_name']);
	unset($setting['csv_delim']);
	unset($setting['csv_escape']);
	unset($setting['csv_charset']);

	//преобразовываем данные в нужный формат для работы с товарами.
	$setting['r_store'] = explode(',', $setting['r_store']);
	//Проверяем не убрал ли пользователь все галочки. если убрал ставим по умолчанию.
	if(empty($setting['r_store'][0])) {$setting['r_store'][0] = 0;}

	$setting['r_lang'] = explode(',', $setting['r_lang']);

	//Проверяем не убрал ли пользователь все галочки. если убрал ставим по умолчанию.
	if(empty($setting['r_lang'][0])) {$setting['r_lang'][0] = 1;}

	#$this->wtfarrey($setting);
  return $setting;
}

//получения параметров парсинга для вывода на странице модуля.
public function getPrsetupToPage($dn_id){
	$setup = $this->getPrSetup($dn_id);

  //приводим опции в состояние.
	$setup['opt_name'] = explode('{next}', $setup['opt_name']);
	$setup['opt_value'] = explode('{next}', $setup['opt_value']);
	$setup['opt_price'] = explode('{next}', $setup['opt_price']);
	$setup['opt_quant'] = explode('{next}', $setup['opt_quant']);
	$setup['opt_quant_d'] = explode('{next}', $setup['opt_quant_d']);
	$setup['opt_data'] = explode('{next}', $setup['opt_data']);

	$setup['opts'] = [];
	//Собераем один массив для вывода опций
	$opt_quant_d = $setup['opt_quant_d'][0];
	foreach ($setup['opt_name'] as $key => $opt_name) {
		$opt_name = explode('{|}', $opt_name);
		if(empty($opt_name[1])){ $opt_name[1]=0; }
		$setup['opts'][$key]['name'] = $opt_name[0];
		$setup['opts'][$key]['opt_id'] = $opt_name[1];
		$setup['opts'][$key]['value'] = $setup['opt_value'][$key];
		$setup['opts'][$key]['price'] = $setup['opt_price'][$key];
		$setup['opts'][$key]['quant'] = $setup['opt_quant'][$key];
		if (!isset($setup['opt_quant_d'][$key])) { 
			$setup['opts'][$key]['quant_d'] = $opt_quant_d; 
		} else { 
			$setup['opts'][$key]['quant_d'] = $setup['opt_quant_d'][$key];
		}
		//преобразовываем дополнительные данные для опций.
		preg_match('#\{required_(.)?\}#', $setup['opt_data'][$key], $required);
		preg_match('#\{price_prefix_(.)?\}#', $setup['opt_data'][$key], $price_prefix);
		if (empty($required[1])){ $required[1] = 0; }
		if (empty($price_prefix[1])){ $price_prefix[1] = '+'; }
		$setup['opts'][$key]['data']['required'] = $required[1];
		$setup['opts'][$key]['data']['price_prefix'] = $price_prefix[1];
	}
	
	//преобразуем данные допуска границ для страницы в парсинге в ИМ
	$setup['grans_permit_list'] = $this->madeGransPermitListToArr($setup['grans_permit_list']);
	#$this->wtfarrey($setup['grans_permit_list']);

	#$this->wtfarrey($setup['grans_permit_list']);
	return $setup;
}

//Сохранить форму настроек пр в магазин
public function savePrsetup($data, $dn_id){
	#$this->wtfarrey($data);
	
	if(empty($data['model'])){ $data['model'] = '';	}
	if(empty($data['sku'])){ $data['sku'] = '';	}
	if(empty($data['name'])) { $data['name'] = ''; }
	if(empty($data['thread'])) { $data['thread'] = 1; }
	if(!isset($data['r_made_url'])){ $data['r_made_url'] = 1;	}
	if(!isset($data['r_made_meta'])){	$data['r_made_meta'] = 0;	}

	if(empty($data['price'])){ $data['price'] = '';	}
	if(empty($data['price_spec'])){ $data['price_spec'] = '';	}
	if(empty($data['r_price_spec_groups'])){ $data['r_price_spec_groups'] = '';	}
	if(empty($data['r_price_spec_date_start'])){ $data['r_price_spec_date_start'] = '';	}
	if(empty($data['r_price_spec_date_end'])){ $data['r_price_spec_date_end'] = '';	}

	if(!isset($data['quant'])){	$data['quant'] = ''; }
	if(empty($data['quant_d'])){ $data['quant_d'] = '';	}
	if(empty($data['r_status_zero'])){ $data['r_status_zero'] = 5; }
	if(empty($data['r_status'])){ $data['r_status'] = 0; }

	if(empty($data['manufac'])){ $data['manufac'] = '';	}
	if(empty($data['manufac_d'])){ $data['manufac_d'] = 0;	}
	if(!isset($data['r_manufac_made_url'])){ $data['r_manufac_made_url'] = 0;	}
	if(!isset($data['r_manufac_made_meta'])){	$data['r_manufac_made_meta'] = 0;	}
	if(empty($data['des'])) {	$data['des'] = '';	}
	if(empty($data['cat'])){ $data['cat'] = '';	}
	if(empty($data['cat_d'])){ $data['cat_d'] = 0;	}
	if(!isset($data['r_cat_perent'])){ $data['r_cat_perent'] = 0;	}
	if(!isset($data['r_cat_made_url'])){ $data['r_cat_made_url'] = 1;	}
	if(!isset($data['r_cat_made_meta'])){	$data['r_cat_made_meta'] = 0;	}
	if(empty($data['des_d'])){ $data['des_d'] = '';	}
	if(empty($data['img'])){ $data['img'] = '';	}
	if(empty($data['img_d'])){ $data['img_d'] = '';	}
	if(empty($data['img_dir'])){ $data['img_dir'] = 'product'; }
	if(empty($data['attr'])){	$data['attr'] = '';	}
	if(empty($data['r_attr_group'])){	$data['r_attr_group'] = 1;	}
	#Разное
	if(!isset($data['upc'])){ $data['upc'] = '';	}
	if(!isset($data['ean'])){ $data['ean'] = '';	}
	if(!isset($data['jan'])){ $data['jan'] = '';	}
	if(!isset($data['isbn'])){ $data['isbn'] = '';	}
	if(!isset($data['mpn'])){ $data['mpn'] = '';	}
	if(!isset($data['location'])){ $data['location'] = '';	}
	if(!isset($data['minimum'])){ $data['minimum'] = 1;	}
	if(!isset($data['subtract'])){ $data['subtract'] = '';	}
	if(!isset($data['length'])){ $data['length'] = '0.00';	}
	if(!isset($data['width'])){ $data['width'] = '0.00';	}
	if(!isset($data['height'])){ $data['height'] = '0.00';	}
	if(!isset($data['length_class_id'])){ $data['length_class_id'] = 1;	}
	if(!isset($data['weight'])){ $data['weight'] = '0.00';	}
	if(!isset($data['weight_class_id'])){ $data['weight_class_id'] = 1;	}
	if(!isset($data['status'])){ $data['status'] = 1;	}
	if(!isset($data['sort_order'])){ $data['sort_order'] = 0;	}

	#Разное, правила
	if(empty($data['r_upc'])){ $data['r_upc'] = 0;	}
	if(empty($data['r_ean'])){ $data['r_ean'] = 0;	}
	if(empty($data['r_jan'])){ $data['r_jan'] = 0;	}
	if(empty($data['r_isbn'])){ $data['r_isbn'] = 0;	}
	if(empty($data['r_mpn'])){ $data['r_mpn'] = 0;	}
	if(empty($data['r_location'])){ $data['r_location'] = 0;	}
	if(empty($data['r_minimum'])){ $data['r_minimum'] = 0;	}
	if(empty($data['r_subtract'])){ $data['r_subtract'] = 0;	}
	if(empty($data['r_length'])){ $data['r_length'] = 0;	}
	if(empty($data['r_width'])){ $data['r_width'] = 0;	}
	if(empty($data['r_height'])){ $data['r_height'] = 0;	}
	if(empty($data['r_length_class_id'])){ $data['r_length_class_id'] = 0;	}
	if(empty($data['r_weight'])){ $data['r_weight'] = 0;	}
	if(empty($data['r_weight_class_id'])){ $data['r_weight_class_id'] = 0;	}
	if(empty($data['r_status'])){ $data['r_status'] = 0;	}
	if(empty($data['r_sort_order'])){ $data['r_sort_order'] = 0;	}
	
	#SEO вкладка
	#Товар
  if(empty($data['seo_url'])){ $data['seo_url'] = '';	}
	if(empty($data['seo_h1'])){ $data['seo_h1'] = '';	}
	if(empty($data['seo_title'])){ $data['seo_title'] = '';	}
	if(empty($data['seo_desc'])){ $data['seo_desc'] = '';	}
	if(empty($data['seo_keyw'])){ $data['seo_keyw'] = '';	}
	#Категории
	if(empty($data['cat_seo_url'])){ $data['cat_seo_url'] = '';	}
	if(empty($data['cat_seo_h1'])){ $data['cat_seo_h1'] = '';	}
	if(empty($data['cat_seo_title'])){ $data['cat_seo_title'] = '';	}
	if(empty($data['cat_seo_desc'])){ $data['cat_seo_desc'] = '';	}
	if(empty($data['cat_seo_keyw'])){	$data['cat_seo_keyw'] = '';	}
	#Производитель
	if(empty($data['manuf_seo_url'])){ $data['manuf_seo_url'] = '';	}
	if(empty($data['manuf_seo_h1'])){	$data['manuf_seo_h1'] = '';	}
	if(empty($data['manuf_seo_title'])){ $data['manuf_seo_title'] = '';	}
	if(empty($data['manuf_seo_desc'])){	$data['manuf_seo_desc'] = '';	}
	if(empty($data['manuf_seo_keyw'])){	$data['manuf_seo_keyw'] = '';	}

	if(empty($data['cache_page'])){	$data['cache_page'] = 0;	}


	//Дополнительные преобразования перед записью в базу
	if (empty($data['r_store'])) {
		$data['r_store'] = '';
		$temp_s = $this->getAllStore();
		foreach ($temp_s as $key_ts => $t_s) {
			if ($key_ts != 0) { $data['r_store'] .= ','.$t_s['store_id']; } else { $data['r_store'] = $t_s['store_id']; }
		}

	} else {
		$data['r_store'] = implode(',',$data['r_store']);
	}

	#Если убрали все галочки в языке тогда записываем выбрать все языки в магазине.
	if(empty($data['r_lang'])) {
		$data['r_lang']='';
		$temp_l = $this->getAllLang();
		foreach ($temp_l as $key_tl => $t_l) {
			if ($key_tl != 0) { $data['r_lang'] .= ','.$t_l['language_id']; } else { $data['r_lang'] = $t_l['language_id']; }
		}

	} else {
		$data['r_lang'] = implode(',',$data['r_lang']);
	}

	//преобразуем блок опций для записи в бд.
	$data['opt_name'] = '';
	$data['opt_value'] = '';
	$data['opt_price'] = '';
	$data['opt_quant'] = '';
	$data['opt_quant_d'] = '';
	$data['opt_data'] = '';
	foreach ($data['opts'] as $key => $opt) {
		if ($key == 0) {
			$data['opt_name'] = $opt['name'].'{|}'.$opt['opt_id'];
			$data['opt_value'] = $opt['value'];
			$data['opt_price'] = $opt['price'];
			$data['opt_quant'] = $opt['quant'];
			$data['opt_quant_d'] = $opt['quant_d'];
			$data['opt_data'] = '{required_'.$opt['data']['required'].'}{price_prefix_'.$opt['data']['price_prefix'].'}';
		} else {
			$data['opt_name'] = $data['opt_name'].'{next}'.$opt['name'].'{|}'.$opt['opt_id'];
			$data['opt_value'] = $data['opt_value'].'{next}'.$opt['value'];
			$data['opt_price'] = $data['opt_price'].'{next}'.$opt['price'];
			$data['opt_quant'] = $data['opt_quant'].'{next}'.$opt['quant'];
			$data['opt_quant_d'] = $data['opt_quant_d'].'{next}'.$opt['quant_d'];
			$data['opt_data'] = $data['opt_data'].'{next}'.'{required_'.$opt['data']['required'].'}{price_prefix_'.$opt['data']['price_prefix'].'}';
		}
	}

	//Преобразуем блок допуска границ.
	if(!empty($data['grans_permit_list'])){

		//перебере
		foreach($data['grans_permit_list'] as $gran_arr_key => &$gran_arr){

			//если в поле не настроена граница парсинга то такое правело не сохраняем.
			if(empty($gran_arr['gran'])){
				unset($data['grans_permit_list'][$gran_arr_key]);
			}else{
				$gran_arr = implode('{!na!}', $gran_arr);
			}

		}
		#$this->wtfarrey($data['grans_permit_list']);
		$data['grans_permit_list'] = implode('{next}', $data['grans_permit_list']);

	}else{

		$data['grans_permit_list'] = '';
	
	}



 	#$this->wtfarrey($data['grans_permit_list']);
  //Сохранение настройки границ в базу..
  $this->db->query("UPDATE ". DB_PREFIX ."pars_prsetup SET
  	model='".$this->db->escape($data['model'])."',
  	sku='".$this->db->escape($data['sku'])."',
  	name='".$this->db->escape($data['name'])."',
  	price='".$this->db->escape($data['price'])."',
  	price_spec='".$this->db->escape($data['price_spec'])."',
  	quant='".$this->db->escape($data['quant'])."',
  	quant_d=".(int)$data['quant_d'].",
  	manufac='".$this->db->escape($data['manufac'])."',
  	manufac_d=".(int)$data['manufac_d'].",
  	des='".$this->db->escape($data['des'])."',
  	des_d='".$this->db->escape($data['des_d'])."',
  	cat='".$this->db->escape($data['cat'])."',
  	cat_d=".(int)$data['cat_d'].",
  	img='".$this->db->escape($data['img'])."',
  	img_d='".$this->db->escape($data['img_d'])."',
  	img_dir='".$this->db->escape($data['img_dir'])."',
  	attr='".$this->db->escape($data['attr'])."',
  	upc='".$this->db->escape($data['upc'])."',
  	ean='".$this->db->escape($data['ean'])."',
  	jan='".$this->db->escape($data['jan'])."',
  	isbn='".$this->db->escape($data['isbn'])."',
  	mpn='".$this->db->escape($data['mpn'])."',
  	location='".$this->db->escape($data['location'])."',
  	minimum='".$this->db->escape($data['minimum'])."',
  	subtract='".$this->db->escape($data['subtract'])."',
  	length='".$this->db->escape($data['length'])."',
  	width='".$this->db->escape($data['width'])."',
  	height='".$this->db->escape($data['height'])."',
  	length_class_id='".$this->db->escape($data['length_class_id'])."',
  	weight='".$this->db->escape($data['weight'])."',
  	weight_class_id='".$this->db->escape($data['weight_class_id'])."',
  	status='".$this->db->escape($data['status'])."',
  	sort_order='".$this->db->escape($data['sort_order'])."',
  	opt_name='".$this->db->escape($data['opt_name'])."',
  	opt_value='".$this->db->escape($data['opt_value'])."',
  	opt_price='".$this->db->escape($data['opt_price'])."',
  	opt_quant='".$this->db->escape($data['opt_quant'])."',
  	opt_quant_d='".$this->db->escape($data['opt_quant_d'])."',
  	opt_data='".$this->db->escape($data['opt_data'])."',
  	grans_permit_list='".$this->db->escape($data['grans_permit_list'])."',
  	seo_url='".$this->db->escape($data['seo_url'])."',
  	seo_h1='".$this->db->escape($data['seo_h1'])."',
  	seo_title='".$this->db->escape($data['seo_title'])."',
  	seo_desc='".$this->db->escape($data['seo_desc'])."',
  	seo_keyw='".$this->db->escape($data['seo_keyw'])."',
  	cat_seo_url='".$this->db->escape($data['cat_seo_url'])."',
  	cat_seo_h1='".$this->db->escape($data['cat_seo_h1'])."',
  	cat_seo_title='".$this->db->escape($data['cat_seo_title'])."',
  	cat_seo_desc='".$this->db->escape($data['cat_seo_desc'])."',
  	cat_seo_keyw='".$this->db->escape($data['cat_seo_keyw'])."',
  	manuf_seo_url='".$this->db->escape($data['manuf_seo_url'])."',
  	manuf_seo_h1='".$this->db->escape($data['manuf_seo_h1'])."',
  	manuf_seo_title='".$this->db->escape($data['manuf_seo_title'])."',
  	manuf_seo_desc='".$this->db->escape($data['manuf_seo_desc'])."',
  	manuf_seo_keyw='".$this->db->escape($data['manuf_seo_keyw'])."'
  	WHERE dn_id=".(int)$dn_id);

  //Сохраняем правила парсинга.
  $this->db->query("UPDATE `". DB_PREFIX ."pars_setting` SET
  	pars_pause='".$this->db->escape($data['pars_pause'])."',
  	action=".(int)$data['action'].",
  	thread=".(int)$data['thread'].",
  	sid='".$this->db->escape($data['sid'])."',
  	grans_permit='".$this->db->escape($data['grans_permit'])."',
  	r_store='".$this->db->escape($data['r_store'])."',
  	r_lang='".$this->db->escape($data['r_lang'])."',
  	r_model=".(int)$data['rules']['model'].",
  	r_sku=".(int)$data['rules']['sku'].",
  	r_name=".(int)$data['rules']['name'].",
  	r_made_url=".(int)$data['r_made_url'].",
  	r_made_meta=".(int)$data['r_made_meta'].",
  	r_price=".(int)$data['rules']['price'].",
  	r_price_spec_groups='".$this->db->escape($data['r_price_spec_groups'])."',
  	r_price_spec_date_start='".$this->db->escape($data['r_price_spec_date_start'])."',
  	r_price_spec_date_end='".$this->db->escape($data['r_price_spec_date_end'])."',
  	r_quant=".(int)$data['rules']['quant'].",
  	r_status_zero=".(int)$data['r_status_zero'].",
  	r_manufac=".(int)$data['rules']['manufac'].",
  	r_manufac_made_url=".(int)$data['r_manufac_made_url'].",
  	r_manufac_made_meta=".(int)$data['r_manufac_made_meta'].",
  	r_des=".(int)$data['rules']['des'].",
  	r_cat=".(int)$data['rules']['cat'].",
  	r_cat_perent=".(int)$data['r_cat_perent'].",
  	r_cat_made_url=".(int)$data['r_cat_made_url'].",
  	r_cat_made_meta=".(int)$data['r_cat_made_meta'].",
  	r_img=".(int)$data['rules']['img'].",
  	r_img_dir=".(int)$data['rules']['img_dir'].",
  	r_attr=".(int)$data['rules']['attr'].",
  	r_opt=".(int)$data['r_opt'].",
		r_attr_group=".(int)$data['r_attr_group'].",
  	r_upc=".(int)$data['r_upc'].",
  	r_ean=".(int)$data['r_ean'].",
  	r_jan=".(int)$data['r_jan'].",
  	r_isbn=".(int)$data['r_isbn'].",
  	r_mpn=".(int)$data['r_mpn'].",
  	r_location=".(int)$data['r_location'].",
  	r_minimum=".(int)$data['r_minimum'].",
  	r_subtract=".(int)$data['r_subtract'].",
  	r_length=".(int)$data['r_length'].",
  	r_width=".(int)$data['r_width'].",
  	r_height=".(int)$data['r_height'].",
  	r_length_class_id=".(int)$data['r_length_class_id'].",
  	r_weight=".(int)$data['r_weight'].",
  	r_weight_class_id=".(int)$data['r_weight_class_id'].",
  	r_status=".(int)$data['r_status'].",
  	r_sort_order=".(int)$data['r_sort_order']."
  	WHERE `dn_id`=".(int)$dn_id
  );
	  #$this->wtfarrey($data);
  //настройки браузера.
  $this->db->query("UPDATE `".DB_PREFIX."pars_browser` SET cache_page = ".(int)$data['cache_page']." WHERE dn_id =".(int)$dn_id);

}

//Получение параметров парсинга для выбора.
public function getParsParam($dn_id){
	//Получаем все параметры парсинга. Если у они есть.
  $params = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_param WHERE dn_id=".$dn_id."  ORDER BY type, id ASC");
  $params = $params->rows;
  foreach ($params as $key => $param) {
  	if($param['type'] == 2) { $params[$key]['name'] = '@ '.$param['name']; }
  }
  return $params;
}

#Фунция перебора категорий. Для первого вызова используй madeCatTree(1)
public function madeCatTree($i=0, $categories=[], $parent_id = 0, $parent_name = '', $language_id=0){
	//моя доработака
  if($i != 0){
  	//Получаем id языка
		$language_id = $this->getLangDef();

  	$query = $this->db->query("SELECT c.category_id, c.parent_id, c.top, d.name FROM ". DB_PREFIX ."category c INNER JOIN ". DB_PREFIX ."category_description d ON c.category_id = d.category_id WHERE d.language_id =".(int)$language_id);
  	
    $category_data = array();
    foreach ($query->rows as $row) {
      $category_data[$row['parent_id']][$row['category_id']] = $row;
    }
    $output = array();
    $output += $this->madeCatTree(0, $category_data);

  }else{
    //Стандартная фунция ниже
    $output = array();

    if (array_key_exists($parent_id, $categories)) {
      if ($parent_name != '') {
        $parent_name .= '->';
      }

      foreach ($categories[$parent_id] as $category) {
        $output[$category['category_id']] = $parent_name . $category['name'];
        $output += $this->madeCatTree(0, $categories, $category['category_id'], $parent_name . $category['name']);
      }
    }
  }

  return $output;
}

//Получение настроек пр в магазин
public function getPrSetup($dn_id){
	$setup = $this->db->query("SELECT * FROM " . DB_PREFIX . "pars_prsetup WHERE `dn_id`=".(int)$dn_id);
  $setup = $setup->row;
  #$this->wtfarrey($setup);
  return $setup;
}

//проверка присуцтвует ли товар
public function checkProduct($data, $setting, $link, $dn_id){
	$do['add'] = ['permit' => 0];
	$do['up'] = ['permit' => 0];
	#Первичная проверка
	if(empty($data[$setting['sid']])){

		if($setting['sid'] == 'model' && $setting['r_model'] == 1){
			$do['up'] = ['permit' => 0,];
			$do['add'] = ['permit' => 1];
		}else{
			#нету идентификатора товара для создания
			$log['sid'] = $setting['sid'];
			$log['link'] = $link;
			$this->log('NoSid', $log, $dn_id);
		}


	}elseif($setting['sid'] == 'sku'){

		$check = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE sku='".$this->db->escape($data['sku'])."' LIMIT 1");
		if($check->num_rows > 0){
			$do['up'] = ['permit' => 1, 'pr_id' => $check->row['product_id']];
			$do['add'] = ['permit' => 0];
		}else{
			$do['up'] = ['permit' => 0];
			$do['add'] = ['permit' => 1];

		}

	}elseif($setting['sid'] == 'model'){

		//если модель парсится.
		if($setting['r_model'] == 2){

			$check = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE model='".$this->db->escape($data['model'])."' LIMIT 1");
			if($check->num_rows > 0){
				$do['up'] = ['permit' => 1, 'pr_id' => $check->row['product_id']];
				$do['add'] = ['permit' => 0];
			}else{
				$do['up'] = ['permit' => 0,];
				$do['add'] = ['permit' => 1];

			}

		}elseif($setting['r_model'] == 1){
			//если модель формируется по умолчанию
			$do['up'] = ['permit' => 0,];
			$do['add'] = ['permit' => 1];
		}

	}elseif($setting['sid'] == 'name'){

		$check = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_description WHERE name='".$this->db->escape($data['name'])."' LIMIT 1");
		if($check->num_rows > 0){
			$do['up'] = ['permit' => 1, 'pr_id' => $check->row['product_id']];
			$do['add'] = ['permit' => 0];
		}else{
			$do['up'] = ['permit' => 0];
			$do['add'] = ['permit' => 1];

		}

	}else{

		$log ='';
		$this->log('addProductNoSidCheck', $log, $dn_id);
	}
	#$this->wtfarrey($setting['sid']);
	return $do;
}

//Фунция добавления url к товару.
#$do - это массив который содержит 2 параметра. 1. Кому присваем, 2 что делаем обновляем или добавляем.
public function addSeoUrl($url, $id, $setting, $langs, $stores, $dn_id, $do){

	//проверяем кому мы создаем url
	if($do['where'] == 'pr'){
		$query = 'product_id=';
	}elseif($do['where'] == 'cat'){
		$query = 'category_id=';
	}elseif($do['where'] == 'manuf'){
		$query = 'manufacturer_id=';
	}else{
		$query = 'error_id='; #Заглушка мало ли. :)
	}
	$logs['where'] = $query.$id;
	//обрезаем если длинее 255 символов
	$url = substr($url, 0, 254);

	//Проверяем с каикм движком мы работаем.
	if($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'opencart2'){

		//смотрим на действие если обновить тогда удаляем запись.
		if($do['what'] == 'up'){
			$this->db->query("DELETE FROM `".DB_PREFIX."url_alias` WHERE `query`='".$this->db->escape($query).(int)$id."'");
		}
		//проверяем если ли такая запись.
		$chek_url = $this->db->query("SELECT * FROM `".DB_PREFIX."url_alias` WHERE `keyword`='".$this->db->escape($url)."'");

		if($chek_url->num_rows > 0){
			$url = $id.'-'.$url;
			$url = substr($url, 0, 254);
		}

		//Создаем url товару
		$this->db->query("INSERT INTO `".DB_PREFIX."url_alias` SET `query`='".$this->db->escape($query).(int)$id."',`keyword`='".$this->db->escape($url)."'");
		$logs['url'] = $url;

	}elseif($setting['vers_op'] == 'ocstore3' || $setting['vers_op'] == 'opencart3'){

		//смотрим на действие если обновить тогда удаляем запись.
		if($do['what'] == 'up'){
			foreach ($langs as $lang) {
				$this->db->query("DELETE FROM `".DB_PREFIX."seo_url` WHERE `query`='".$this->db->escape($query).(int)$id."' AND `language_id`=".(int)$lang['language_id']);
			}
		}
		//проверяем если ли такая запись.
		$chek_url = $this->db->query("SELECT * FROM `".DB_PREFIX."seo_url` WHERE `keyword`='".$this->db->escape($url)."'");

		if($chek_url->num_rows > 0){
			$url = $id.'-'.$url;
			$url = substr($url, 0, 254);
		}

		foreach($langs as $lang){
			//Создаем url товару
			foreach ($stores as $store) {
				$this->db->query("INSERT INTO `".DB_PREFIX."seo_url` SET
					`store_id`=".$store['store_id'].",
					`language_id`=".(int)$lang['language_id'].",
					`query`='".$this->db->escape($query).(int)$id."',
					`keyword`='".$this->db->escape($url)."'");
			}
		}
		$logs['url'] = $url;
	}
	$this->log('LogAddSeoUrl', $logs, $dn_id);
}

//Поучаем id атрибута
public function getIdAttr($name){
	$name = trim($name);
	#Убираем двое точие в конце атрибута.
	if(substr($name, -1) == ':'){ $name = substr($name, 0, -1); }
	#Вдруг имя атрибута стало пустым.
	if(empty($name)){
		return 0;
	}

	$rows = $this->db->query("SELECT `attribute_id` as attr_id FROM `".DB_PREFIX."attribute_description` WHERE `name` ='".$this->db->escape($name)."'");
	if($rows->num_rows == 0){
		$attr_id = 0;
	}else{
		$attr_id = $rows->row['attr_id'];
	}
	return $attr_id;

}

//Создаем атрибут и возврашаем его id
public function addAttr($name, $langs, $setting, $dn_id){
	$name = trim($name);
	$attr_id = 0;
	#Убираем двое точие в конце атрибута.
	if(substr($name, -1) == ':'){ $name = substr($name, 0, -1); }
	#Вдруг имя атрибута стало пустым.
	if(empty($name)){
		return $attr_id;
	}

	$this->db->query("INSERT INTO `" . DB_PREFIX . "attribute` SET `attribute_group_id`='".(int)$setting['r_attr_group']."',`sort_order`=0");
	$attr_id = $this->db->getLastId();

	//проверяем что бы создался
	if($attr_id > 0){
		#Записываем в дескрипшн.
		foreach($langs as $lang){
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attr_id . "', language_id = '" . (int)$lang['language_id'] . "', name = '" . $this->db->escape($name) . "'");
		}
		//Сообшаем о создании нового атрибута.
		$log = ['attr_name' => $name, 'r_attr_group' => $setting['r_attr_group']];
		$this->log('AddNewAttr', $log, $dn_id);
	}else{
		$log = ['attr_name' => $name, 'r_attr_group' => $setting['r_attr_group']];
		$this->log('NoAddNewAttr', $log, $dn_id);
	}

	return $attr_id;
}

//Создаем производителя.
public function addManuf($form, $langs, $setting, $stores, $dn_id){
	$name = trim($form['manufac']);
	if(empty($name)){
		return 0;
	}
	//Сео данные
	$data['meta_h1'] = '';
	$data['meta_title'] = '';
	$data['meta_description'] = '';
	$data['meta_keyword'] = '';

	//Проверяем работу с SEO данными
	if($setting['r_manufac_made_meta'] ==1){
		$data['meta_h1'] = $form['manuf_seo_h1'];
		$data['meta_title'] = $form['manuf_seo_title'];
		$data['meta_description'] = $form['manuf_seo_desc'];
		$data['meta_keyword'] = $form['manuf_seo_keyw'];
	}

	$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '".$this->db->escape($name)."', sort_order = 0");
	$manuf_id = $this->db->getLastId();

	//Проверяем на каком движке работаем. Если OcStore тогда добавляем еше manufacturer_description
	if($setting['vers_op']=='ocstore2'){

		//Добавляем в таблицу oc_manufacturer_description
		foreach($langs as $lang){
			$this->db->query("INSERT INTO `" . DB_PREFIX . "manufacturer_description`	SET
				`manufacturer_id`=".(int)$manuf_id.",
				`language_id`=".(int)$lang['language_id'].",
				`name`='".$this->db->escape($name)."',
				`meta_h1`='".$this->db->escape($data['meta_h1'])."',
				`meta_title`='".$this->db->escape($data['meta_title'])."',
				`meta_description`='".$this->db->escape($data['meta_description'])."',
				`meta_keyword`='".$this->db->escape($data['meta_keyword'])."'
				");
		}

	}elseif($setting['vers_op']=='ocstore3'){

		//Добавляем в таблицу oc_manufacturer_description
		foreach($langs as $lang){
			$this->db->query("INSERT INTO `" . DB_PREFIX . "manufacturer_description` SET
				`manufacturer_id`=".(int)$manuf_id.",
				`language_id`=".(int)$lang['language_id'].",
				`meta_h1`='".$this->db->escape($data['meta_h1'])."',
				`meta_title`='".$this->db->escape($data['meta_title'])."',
				`meta_description`='".$this->db->escape($data['meta_description'])."',
				`meta_keyword`='".$this->db->escape($data['meta_keyword'])."'
				");
		}

	}

	//Создаем таблицу c_manufacturer_to_store
	foreach ($stores as $store) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "manufacturer_to_store` SET `manufacturer_id`=".(int)$manuf_id.",`store_id`=".$store['store_id']);
	}


	$this->log('addManuf', $log = ['id'=>$manuf_id, 'name'=>$name], $dn_id);

	//////////////////////////////////////////////////
	//Работа с SEO_URL
	// 0 - Незаполнять
	// 1 - Создать из имени товара
	// 2 - Создать по шаблону заполненому на вкладке SEO
	////////////////////////////////////////////////////
	if($setting['r_manufac_made_url'] == 1){

		//Получаем юрл из имени.
		if(!empty($name)){
			$manuf_url = $this->madeUrl($name);

			//Записываем url
			$do = ['where'=>'manuf','what'=>'add'];
			$this->addSeoUrl($manuf_url, $manuf_id, $setting, $langs, $stores, $dn_id, $do);
		}else{
			$logs['name'] = 'manufacture name';
			$this->log('badUrl', $logs, $dn_id);
		}

	}elseif($setting['r_manufac_made_url'] == 2){

		if(!empty($form['manuf_seo_url'])){
			//Получаем юрл из имени.
			$manuf_url = $this->madeUrl($form['manuf_seo_url']);

			//Записываем url
			$do = ['where'=>'manuf','what'=>'add'];
			$this->addSeoUrl($manuf_url, $manuf_id, $setting, $langs, $stores, $dn_id, $do);
		}else{
			$logs['name'] = 'seo_url';
			$this->log('badUrl', $logs, $dn_id);
		}
	}

	return $manuf_id;
}

//Преобразование фото.
public function madeImgArrey($image, $url){
	//преобразовываем фото
	if(!empty($image)){
		$domain = parse_url($url);
		#Делаем из строки массив категорий.
		$image  = str_replace('{csvnc}','{!na!}',$image);
		$imgs = explode('{!na!}', $image);
		#Убираем из массива пустые значения
		foreach($imgs as $var){
			//Удаляем лишние проблеы
			$var = trim($var);
			if($var != false){
				//Добавлем нужные элементы к ссылке.
				if($var[0] == '/' && $var[1] != '/'){
					$var = $domain['scheme'].'://'.$domain['host'].$var;

				}elseif($var[0] == '/' && $var[1] == '/'){
					$var = str_ireplace('//', $domain['scheme'].'://',$var);
				}
				$img[] = $var;
			}
		}

		if(!empty($img)){
			$img = array_unique($img);
		}else{
			$img = [];
		}
	}else{
			$img = [];
	}
	#$this->wtfarrey($img);
	return $img;
}

public function dwImagToProduct($dn_id, $imgs, $dir, $under){
	//Фунцяи скачивает фото, расскладывает и возврашает массив для записи в БД

	#фото для товара
	$path = DIR_IMAGE.'catalog/';
	$href = [];
	#Удаляем слешы из начала и конца имени директории фото. Подготавливаем директорию под загрузку.
	if($dir[0] == '/'){ $dir = substr($dir, 1);}
	if(substr($dir, -1) == '/'){ $dir = substr($dir, 0, -1); }
	//Убераем обратные слеши, аля я на винде :)
	$dir = str_replace('\\', '/', $dir);
	#путь к директории на загрузку.
	$path .=$dir;
	//Если включена переменная подпапки зарание создаем подпапки.
	if($under){
		for($i=0;$i<10;$i++){
			$dir_add = $path.'/'.$i;
			if(!is_dir($dir_add)){ mkdir($dir_add, 0755, true); }
		}
	}else{
		$dir_add = $path;
		if(!is_dir($dir_add)){ mkdir($dir_add, 0755, true); }
	}

	//Делаем массив из директорий, вдруг комунто нужно много вложенности.

	#загатовка для чистки массива
	$search = [" ","'","+"];
	$replace = ['_','',''];
	$uder_dir = (int)substr(microtime(), -1); #Получаем цифру для вычисления под директории.
	#$this->wtfarrey($imgs);
	$imgs_chunk = array_chunk($imgs, 10);
	#$this->wtfarrey($imgs_chunk);

	foreach($imgs_chunk as $chunk){
		$data_img = $this->curlImg($chunk, $dn_id);
		#$this->wtfarrey($data_img);
		
		//Перебераем массив
		foreach($data_img as $key => $img){
			$img_temp = str_replace($search, $replace, urldecode($img['url']));
			#получаем имя фото. И отрезаем от него хвостик.
			$name = preg_replace('#\?(.*)#', '', basename($img_temp));
			$name = $this->symbolToEn($name);

			//проверяем длину имени фото, если длина больше 250 символов, ссылка не верна
			if(strlen($name) > 250) {
				$name = 'sp-bad-url-img.jpg';
			}

			//Проверяем есть ли расширение файла. Если нет, добавляем.
			$exec = pathinfo($name);
			if (empty($exec['extension'])) { $name .='.jpg';}

			#есои файл скачался.
			if(!empty($img['img'])){
				#Сохраняем фото
				#$this->wtfarrey($file);
				#если выбрано сохранять по подпапкам.
				if($under){
					$path_img = $path.'/'.$uder_dir.'/'.$name;
					//Проверяем есть ли такое фото. Если да то добавляем цифрув начала имени.
					for($i=1;$i>0;){
						if(file_exists($path_img)){
							$path_img = $path.'/'.$uder_dir.'/'.$i.'-'.$name;
							$i++;
						}else{
							$i=0;
						}
					}

					#сохранение фото на диск
					file_put_contents($path_img, $img['img']);

					//Финальное имя для базы данных
					$href[] = 'catalog/'.$dir.'/'.$uder_dir.'/'.basename($path_img);

				}else{
					$path_img = $path.'/'.$name;
					//Проверяем есть ли такое фото. Если да то добавляем цифрув начала имени.
					for($i=1;$i>0;){
						if(file_exists($path_img)){
							$path_img = $path.'/'.$i.'-'.$name;

							$i++;
						}else{
							$i=0;
						}
					}

					#сохранение фото на диск
					file_put_contents($path_img, $img['img']);

					//Финальное имя для базы данных
					$href[] = 'catalog/'.$dir.'/'.basename($path_img);
					#$this->wtfarrey($href);
				}#конец under

			}else{//Для описания что бы не сбивать порядок фото в массиве. 

				//Через жопу определяем парсятся фото в описани или нет. Если фунция зайдет то переделать под отдельный маячок.
				if($dir = 'description' && $under == 1){
					$href[] = ''; #так нужно что бы не сбить порядок ключей в массиве фото.
				}

			}

		}
	}

	return $href;
}

//Преобразования фото для пред просмотра.
public function madeImgShow($image, $dn_id){
	//обьявляем путь.
	$dir_image = 'image/catalog/';
	$path_check = DIR_IMAGE.'catalog/SPshow';

	// Создаем директорию если пользовател ее затер.
	if(!is_dir($path_check)){ mkdir($path_check, 0755, true); }

	//делем массив для много поточности
	$imgs_chunk = array_chunk($image, 10);
	foreach($imgs_chunk as $chunk){
		$data_img = $this->curlImg($chunk, $dn_id);

		foreach($data_img as $key => $img){
			$old_name = md5(basename($img['url'].$key)).'.jpg';
			$path_img = $dir_image.'SPshow/'.$old_name;
			file_put_contents('../'.$path_img, $img['img']);
			$data_img[$key] = '../'.$path_img;
		}

	}

	#$this->wtfarrey($image);
	return $data_img;
}

//Преобразование категорий
public function madeCatArrey($category){
	//Преобразования категорий
	if(!empty($category)){
		#Делаем из строки массив категорий.
		$category = str_replace('{csvnc}','{!na!}',$category);
		$cats = explode('{!na!}', $category);
		#Убираем из массива пустые значения
		foreach($cats as $var){
			if($var != false){
				$cat[] = $var;
			}
		}

		if(empty($cat)){
			$cat = [];
		}
		return $cat;
	}

}
//найти сушествует такая категория или нет.
public function findCategory($cat_way){
	$cat_way = trim($cat_way);
	$cat_id = 0;
	if(!empty($cat_way)){
		$cat_tree = $this->madeCatTree(1);

		if(!$cat_id =	array_search($cat_way, $cat_tree)){ $cat_id = 0; }
		#$this->wtfarrey($cat_tree);
		#$this->wtfarrey($cat_way);
	}
	return $cat_id;
}

//Получить id категорий которые мы запрашиваем в массиве Масси должен быть одномерным. Значения идут от родительской категории к дочерней.
//Возврашается ассоциативный массив где ключи в обратном порядке. От дочерней к родительской!!!
public function getCategorysId($cats){
	$ids[0] = 0; #Заглушка на всякий случай.

	if(!empty($cats)){
		//Получаем дерево категорий что есть в магазине.
		$cat_tree = $this->madeCatTree(1);
		//Формируем по очередности имена категорий и проверяем есть ли в магазине.
		$cat_way = '';
		foreach ($cats as $key => $cat) {
			if ($key == 0) {
				$cat_way = trim($cat);
				if(!$ids[0] =	array_search($cat_way, $cat_tree)){ $ids[0] = 0; }

			} else {
				$cat_way .= '->'.trim($cat);
				if(!$ids[] =	array_search($cat_way, $cat_tree)){ $ids[] = 0; }
			}
		}

	}
	$ids = array_reverse($ids);
	//Возврашаем массив.
	#$this->wtfarrey($ids);
	return $ids;
}

//Фунция добавления товара в категорию
//Принимает массив id категорий. id товара. И настройки
public function addProdToCat($cats, $pr_id, $setting){
	#$this->wtfarrey($cats);
	$log = '';
	//Производим запись данных в категорию.
	foreach ($cats as $key => $cat_id) {

		if($key == 0) { $log = $cat_id; } else { $log .= ','.$cat_id;}

		//Если это только первая итерация. И это движок ocStor тогда мы записываем как главная категория.
		if( $key ==0 && ($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'ocstore3')){
			//Добавление товар в категорию ocStore
			$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "product_to_category SET
						product_id = '" . (int)$pr_id . "',
						category_id = '" . (int)$cat_id . "',
						main_category = 1");
		}else{
			//Добавление товар в категорию Opencart
			$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "product_to_category SET
				product_id = '" . (int)$pr_id . "',
				category_id = '" . (int)$cat_id."'");
		}

		//Останавливаем зпись товара в категории. По настройке показывать в.
		#0 Только в младшей.
		#1 В младшей и в одной родительской.
		#2 В младщей и во всех родительских.
		if ($setting['r_cat_perent'] == 0) { break; } elseif ($setting['r_cat_perent'] == 1 && $key == 1) { break; }
	}

	//отправляем лог 
	return $log;
}

//Добавляем атрибу в товар.
public function addAttrToProduct($pr_id, $attr, $langs, $dn_id){
	#$this->wtfarrey($attr);
	if( !empty($attr[1]) ){ $attr[1] = trim($attr[1]); }	
	//перед тем как производить запись новых атриубтов в товар производим удаление.
	$this->db->query("DELETE FROM `".DB_PREFIX."product_attribute` WHERE `product_id`=".(int)$pr_id." AND `attribute_id`=".(int)$attr['id']);
	//Добавляем
	foreach($langs as $lang){
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_attribute` SET `product_id`=".(int)$pr_id.", `attribute_id`=".(int)$attr['id'].", `language_id`=".(int)$lang['language_id'].", `text`='".$this->db->escape($attr[1])."'");
	}
	$log = ['name' => $attr[0], 'value'=>$attr[1], 'attr_id'=> $attr['id']];
	#$log['cat_id'] = $data['cat_id'];
	$this->log('addAttrToProductLog', $log, $dn_id);
}

//Добавление акционных цен.
public function addPriceSpecToProduct($price_spec, $setting, $pr_id, $dn_id){
	//Получаем список выбранных групп пользователей.
	$cast_groups = $this->getGroupCustomer($setting);

	//Переменная для логов.
	$group = '';

	#$this->wtfarrey($cast_groups);

	//перебираем все группы.
	foreach ($cast_groups as $cast_group) {
		//Удаляем акцию если такая есть.
		$special_id = $this->db->query("SELECT product_special_id FROM ".DB_PREFIX."product_special 
			WHERE product_id=".(int)$pr_id." AND customer_group_id=".(int)$cast_group['customer_group_id']);

		if($special_id->num_rows == 0){
			
			//Создаем заново акцию.
			$this->db->query("INSERT INTO ".DB_PREFIX."product_special SET
				product_id = ".(int)$pr_id.",
				customer_group_id = ".(int)$cast_group['customer_group_id'].",
				priority = 1,
				price = ".$price_spec.",
				date_start = '".$this->db->escape($setting['r_price_spec_date_start'])."',
				date_end = '".$this->db->escape($setting['r_price_spec_date_end'])."'");

		}elseif($special_id->num_rows > 0){

			//Создаем заново акцию.
			$this->db->query("UPDATE ".DB_PREFIX."product_special SET
				product_id = ".(int)$pr_id.",
				customer_group_id = ".(int)$cast_group['customer_group_id'].",
				priority = 1,
				price = ".$price_spec.",
				date_start = '".$this->db->escape($setting['r_price_spec_date_start'])."',
				date_end = '".$this->db->escape($setting['r_price_spec_date_end'])."' 
				WHERE product_special_id =".$special_id->row['product_special_id']);

		}

		//Записываем id в переменную для логов.
		$group .= ','.$cast_group['customer_group_id'];

	}

	$logs = [
						'price_spec'=>$price_spec,
						'group'=>$group,
						'date'=> $setting['r_price_spec_date_start'].' - '.$setting['r_price_spec_date_end']
					];
	$this->log('addPriceSpecToProduct', $logs, $dn_id);
}

//Удаление акции
public function delPriceSpecToProduct($price_spec, $setting, $pr_id, $dn_id){
	$sql = "DELETE FROM ".DB_PREFIX."product_special WHERE product_id=".(int)$pr_id;
	#$this->wtfarrey($sql);
	$this->db->query($sql);
}

//Создание категорий исходя из дерева категорий, и сушествующих категорий.
public function addCat($form, $setting, $langs, $stores,$dn_id){

	$cat = $form['cat'];
	#$this->wtfarrey($form);
	#Получаем категории из базы в нужном виде.
	$cat_tree = $this->madeCatTree(1);
	#Проверяем и создаем категории если такой нет.
	$cat_way = '';

	//Данные по умолчани для создания категорий.
	$cat_id = 0;
	$data['parent_id'] = 0; #id родительско категории
	$data['image'] = '';
	$data['top'] = 0;
	$data['column'] = 1;
	$data['sort_order'] = 0;
	$data['status'] = 1;
	//Сео данные
	$data['meta_h1'] = '';
	$data['description'] = '';
	$data['meta_title'] = '';
	$data['meta_description'] = '';
	$data['meta_keyword'] = '';

	if($setting['r_cat_made_meta'] ==1){
		$data['meta_h1'] = $form['cat_seo_h1'];
		$data['meta_title'] = $form['cat_seo_title'];
		$data['meta_description'] = $form['cat_seo_desc'];
		$data['meta_keyword'] = $form['cat_seo_keyw'];
	}

	//Язык по умолчанию.
	$language_default_id = $this->getLangDef();

	//проверяем есть ли в языках стандартный язык системы. Если нет добавляем.
	if(array_search($language_default_id, array_column($langs, 'language_id')) === false){
		$langs[] = ['language_id' => $language_default_id];
	}

	#$this->wtfarrey($langs);

	foreach($cat as $key => $name){
		#Очешаем от лишнего
		$name = trim($name);
		#Составляем путь для сравнения
		if($key == 0){$cat_way = $name;}else{$cat_way = $cat_way .= '->'.$name;}

		#Сравниваем.
		$cat_id =	array_search($cat_way, $cat_tree);

		#Если такой категории нет создаем ее.
		if($cat_id == 0){
			#Узнаем родительская это категория или нет. Что бы понять как ее создать
			if($key == 0){
				$data['top'] = 1;
				$data['parent_id'] = 0;
			}else{
				$data['top'] = 0;
			}

			//Добавляем в базу oc_category
			$this->db->query("INSERT INTO " . DB_PREFIX . "category SET
				parent_id = '" . (int)$data['parent_id'] . "',
				`top` = '" . (int)$data['top'] . "',
				`column` = '" . (int)$data['column'] . "',
				sort_order = '" . (int)$data['sort_order'] . "',
				status = '" . (int)$data['status'] . "',
				date_modified = NOW(),
				date_added = NOW()");

			$cat_id = $this->db->getLastId();

			//проверяем стоит ли дальше создавать
			if($cat_id){

				//Проверяем версию движка для правильного заполнения.
				$mh1 = '';
				if($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'ocstore3'){
					$mh1 = ",meta_h1='".$this->db->escape($data['meta_h1'])."'";
				}

				//Добавлем в базу oc_category_description
				foreach($langs as $lang){
					$sql = "INSERT INTO " . DB_PREFIX . "category_description SET
						category_id = '" . (int)$cat_id . "',
						language_id = '" . (int)$lang['language_id'] . "',
						name = '" . $this->db->escape($name) . "',
						description = '". $this->db->escape($data['description']) ."',
						meta_title = '" . $this->db->escape($data['meta_title']) . "',
						meta_description = '" . $this->db->escape($data['meta_description']) . "',
						meta_keyword = '" . $this->db->escape($data['meta_keyword'])."'".$mh1;
					$this->db->query($sql);
				}

				//Добавляем в таблицу oc_category_to_store
				foreach ($stores as $store){
					$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "category_to_store SET
						category_id = '" . (int)$cat_id . "',
						store_id = " . $store['store_id']);
				}
				//////////////////////////////////////////////////
				//Работа с SEO_URL
				// 0 - Незаполнять
				// 1 - Создать из имени товара
				// 2 - Создать по шаблону заполненому на вкладке SEO
				////////////////////////////////////////////////////
				if($setting['r_cat_made_url'] == 1){

					//Получаем юрл из имени.
					if(!empty($name)){
						$cat_url = $this->madeUrl($name);

						//Записываем url
						$do = ['where'=>'cat','what'=>'add'];
						$this->addSeoUrl($cat_url, $cat_id, $setting, $langs, $stores, $dn_id, $do);
					}else{
						$logs['name'] = 'category name';
						$this->log('badUrl', $logs, $dn_id);
					}

				}elseif($setting['r_cat_made_url'] == 2){

					if(!empty($form['cat_seo_url'])){
						//Получаем юрл из имени.
						$cat_url = $this->madeUrl($form['cat_seo_url']);

						//Записываем url
						$do = ['where'=>'cat','what'=>'add'];
						$this->addSeoUrl($cat_url, $cat_id, $setting, $langs, $stores, $dn_id, $do);
					}else{
						$logs['name'] = 'seo_url';
						$this->log('badUrl', $logs, $dn_id);
					}
				}


			}#Конец проверки на созданную категорию в oc_category


			#передаем id родителя для следующей категории.
			$data['parent_id'] = $cat_id;
			#Пишем в лог информацию о создании категории.
			$log['id'] = $cat_id;
			$log['cat_way'] = $cat_way;

			$this->log('addCat', $log, $dn_id);
		}else{
			#передаем id родителя для следующей категории.
			$data['parent_id'] = $cat_id;
		}

	}
	//Тестовая фунция репаир категорий. Незнаю нужно или нет но потестируем. Магическая хрень, делаем категории видимыми.
	$this->repairCategories();
	if(empty($cat_id)) $cat_id = 0;
	#return $cat_id;
}

//Преобразование атрибутов.
public function madeAttrArrey($attrs){
	if(!empty($attrs)){
		//Для совместимости переводим в единый стандарт.
		$attrs = str_replace('{csvnc}','{!na!}',$attrs);
		$attrs = explode('{!na!}', $attrs);
		#$this->wtfarrey($attrs);
		//Удаляем все пустые значения из начала массива.
		foreach($attrs as $key => $var){

			if(empty(trim($var))){
	      unset($attrs[$key]);
	    }else{
	      break;
	    }

		}
		$attrs = array_values($attrs);
		#$this->wtfarrey($attrs);
		//Здесь закопано 2 процесса. 1) записываем в массив значение. 2) Делим отдельный атрибут на отдельный массив.
		$i = 1;
		foreach($attrs as $key => $var){
			$attr[$i][] = $var;

			if($key % 2 ==1){
				$i++;
			}

		}

		//Проверяем что предыдушие правила не вычистили все что было в массиве.
		if (!empty($attr)) {
			#$this->wtfarrey($attr);
			//Удаляем атрибуты без имени или без значения. Те массивы где не полная пара.
			foreach($attr as $key => $value){
				if(empty($value[1])){
					$attr[$key][1]  = '';
				}
			}
		} else {
			//Если масси пришел пустым все же отдаем его.
			$attr = [];
		}

	} else {
		$attr = [];
	}
	return $attr;
}

//Функция создания ссылок. На вход должна поступать строка. На выходе строка форматированная для url
public function madeUrl($data){
	//Преобразовываем сушности.
	$data = html_entity_decode($data);
	//переводим русские символы в латиницу
  $data = $this->symbolToEn($data);
	//Заменяем все пробелы на тире
	$data = str_replace(' ', '-', $data);
	//Удалем все кроме латинских букв, цифр и знака тире.
 	$data = preg_replace('#[^A-Za-z0-9\-\_]#', '', $data);
 	//Наводим марафет, убераем по два и более тре подряд.
 	$data = preg_replace('/-+/', '-', $data);
 	$data = preg_replace('/_+/', '_', $data);
 	//Приводим к нижнему регистру. Незнаю зачем но кажется так луче :)
  $data = mb_strtolower($data);

 	return $data;
}

//Получаем сушествующие группы атрибутов.
public function getAttrGroup(){
	//Получаем id языка
	$language_id = $this->db->query("SELECT language_id FROM ".DB_PREFIX."language WHERE status=1");
	$language_id = $language_id->row['language_id'];
	if(empty($language_id)){ $language_id = 0;}

	//Немного по тупому сделал. Но что имеем. Пока что с языками все сложно.
	$attr_group = [];
	$attr_group = $this->db->query("SELECT * FROM `".DB_PREFIX."attribute_group_description` WHERE language_id='".$language_id."'");
	$attr_group = $attr_group->rows;
	$attr_group = array_column($attr_group, 'name', 'attribute_group_id');
	unset($attr_group[1]);

	return $attr_group;
}

//Получаем id мануфактуры
public function getIdManuf($name){
	$name = trim($name);
	$rows = $this->db->query("SELECT * FROM `".DB_PREFIX."manufacturer` WHERE `name`='".$this->db->escape($name)."'");

	if($rows->num_rows == 0){
		$manuf_id = 0;
	}else{
		$manuf_id = $rows->row['manufacturer_id'];
	}
	return $manuf_id;
}

//Получаем список магазинов.
public function getStore($setting){
	//Полюбому отдаем массив
	$store = [];
	foreach ($setting['r_store'] as $key => $value) {
		$store[] = ['store_id' => $value];
	}
	#$this->wtfarrey($store);
	return $store;
}
//Получаем список магазинов.
public function getAllStore(){
	$store = [];
	$store[] = ['store_id'=>0, 'name'=>'Главный'];
	$stores = $this->db->query("SELECT store_id, name FROM ".DB_PREFIX."store");
	if ($stores->num_rows > 0) {
		foreach ($stores->rows as $row) {
			$store[] = $row;
		}
	}

	#$this->wtfarrey($store);
	return $store;
}

//Получаем список всех групп покупателей.
public function getAllGroupCustomer(){
	$cast_groups = $this->db->query("SELECT c.customer_group_id, d.name
		FROM ".DB_PREFIX."customer_group c INNER JOIN ".DB_PREFIX."customer_group_description d
		ON c.customer_group_id = d.customer_group_id
		WHERE d.language_id = 1");
	$cast_groups = $cast_groups->rows;

	if (!empty($cast_groups)) {
		$cast_groups = array_column($cast_groups, 'name', 'customer_group_id');
	}

	return $cast_groups;
}

//Получаем список всех групп покупателей.
public function getGroupCustomer($setting){
	//проверяем какую группу выбрал пользователь
	$sql = '';
	if ($setting['r_price_spec_groups'] !='all') {
		$sql = ' WHERE customer_group_id='.$setting['r_price_spec_groups'];
	}
	$cast_groups = $this->db->query("SELECT customer_group_id FROM ".DB_PREFIX."customer_group".$sql);
	$cast_groups = $cast_groups->rows;

	return $cast_groups;
}

//получаем единицы длины
public function getLengthClassId(){
	$length_class_id = $this->db->query("SELECT * FROM ".DB_PREFIX."length_class_description WHERE language_id = 1");
	$length_class_id = $length_class_id->rows;
	return $length_class_id;
}

//получаем единицы веса
public function getWeightClassId(){
	$weight_class_id = $this->db->query("SELECT * FROM ".DB_PREFIX."weight_class_description WHERE language_id = 1");
	$weight_class_id = $weight_class_id->rows;
	return $weight_class_id;
}

//Получаем выбранный язык
public function getLang($setting){
	//проверяем какой язык выбра пользователем. По умолчанию все.
	$langs = [];

	foreach ($setting['r_lang'] as $key => $value) {
		$langs[] = ['language_id' => $value];
	}

	#$this->wtfarrey($langs);
	return $langs;
}

//Получаем список языков
public function getAllLang(){
	//Проверяем какой язык выбран.
	$lang = $this->db->query("SELECT language_id, name FROM ".DB_PREFIX."language");
	return $lang->rows;
}
//Получаем список статусов
public function getAllStockStatus(){

	//Получаем id языка
	$language_id = $this->db->query("SELECT language_id FROM ".DB_PREFIX."language WHERE status=1");
	$language_id = $language_id->row['language_id'];
	if(empty($language_id)){ $language_id = 0;}

	//Проверяем какой язык выбран.
	$stock_status = $this->db->query("SELECT * FROM `".DB_PREFIX."stock_status` WHERE language_id =".$language_id);

	#$this->wtfarrey($stock_status->rows);
	return $stock_status->rows;
}

//получаем список опций
public function getAllOpts(){
	$options = $this->db->query("SELECT o.option_id, d.name FROM `".DB_PREFIX."option` o INNER JOIN ".DB_PREFIX."option_description d ON o.option_id = d.option_id WHERE d.language_id =1 ORDER BY o.option_id");
	$options = $options->rows;
	#$this->wtfarrey($options);
	return $options;
}

//Определяем статус для товара
public function getProductStatus($data, $setting){
	////////////////////////////////////////////////
	// 1 = Не отключать товар
	// 2 = Отключить товар
	// 3 = Отключить товар при нулевом остатке
	////////////////////////////////////////////////

	#По умолчанию товарам
	$status = 1;
	$data['status'] = (int)$data['status'];
	if ($data['status'] == 1) {
		$status = 1;
	} elseif ($data['status'] == 2) {
		$status = 0;
	} elseif (($data['status'] == 3) && $setting['r_quant'] && $data['quant'] == 0) {
		$status = 0;
	}

	#$this->wtfarrey($status);
	return $status;
}

//Обрабатываем логические и математические операторы.
public function madeLogicalMathem($data, $type='int'){
	#$this->wtfarrey($data);
	$var = '';
	$data = explode('{|}', $data);

	//перебераем все варианты логического оператора
	foreach ($data as $value) {

		//если поле пустое то ставим 0
		//if(empty($value)){
		if( $type == 'int' && empty($value)){

			unset($value);
			$value[0] = '0';
		
		}elseif( $type == 'str' && empty($value) && $value !='0'){
			
			unset($value);
			$value[0] = '';

		}else{

			##############################
			# разбер математических фунций
			##############################
			#$this->wtfarrey($value);
			//Делим строку на массив в перемешку с оперантами.
			$value = preg_split('#(\{.\})#', $value, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			
			//Если массив пустой, то присваеваем пустоту первому ключу, так как в конце будем именно с него брать значение.
			if(empty($value)){$value[0] = '';}
			
			//Если первый элемент массива не значение а оперант, то добавляем перед ним ноль. Что бы массив начинался с числа.
			if(preg_match('#\{.\}#', $value[0])){ array_unshift($value, 0); }

			//Запускаем цикл который будет перебирать массив и делать математические действия.
			$i =1;
		  while($i){
		  	#$this->wtfarrey($value);
		    //Если нету операнта, и второго значения значит уже не выполняем математику.
		    if(!isset($value[1]) || !isset($value[2])){
		      $i = 0;
		    }else{

		      if($value[1] == '{+}'){

		        $value[0] = (float)str_replace(array(' ', ','), array('', '.'), $value[0]) + (float)str_replace(array(' ', ','), array('', '.'), $value[2]);
		        unset($value[1]);
		        unset($value[2]);
		        $value = array_values($value);

		      }elseif($value[1] == '{*}'){

		        $value[0] = (float)str_replace(array(' ', ','), array('', '.'), $value[0]) * (float)str_replace(array(' ', ','), array('', '.'), $value[2]);
		        unset($value[1]);
		        unset($value[2]);
		        $value = array_values($value);

		      }elseif($value[1] == '{-}'){

		        $value[0] = (float)str_replace(array(' ', ','), array('', '.'), $value[0]) - (float)str_replace(array(' ', ','), array('', '.'), $value[2]);
		        unset($value[1]);
		        unset($value[2]);
		        $value = array_values($value);

		      }elseif($value[1] == '{/}'){

		        if(empty((float)$value[2])){ $value[2] = 1;}
		       
		        $value[0] = (float)str_replace(array(' ', ','), array('', '.'), $value[0]) / (float)str_replace(array(' ', ','), array('', '.'), $value[2]);
		        unset($value[1]);
		        unset($value[2]);
		        $value = array_values($value);

		      }
		    }
		  }
		}

		//выбираем какую цену оставить
		#$this->wtfarrey($value);

		if( $type == 'int' && !empty($value[0])){

			$var = $value[0];
			break;

		}elseif( $type == 'str' && $value[0] !== '' ){

			$var = $value[0];
			break;


		}

	}
	#$this->wtfarrey($var);
	return $var;
}

//фунйция подготовки данных option
public function madeOption($data){
	$opts = [];

	//вырезаем ненужное из опции
	$data['opt_name'] = str_replace('{!na!}', '', $data['opt_name']);
	$data['opt_value'] = str_replace('{csvnc}', '{!na!}', $data['opt_value']);
	$data['opt_value'] = str_replace('{!na!}{!na!}', '{!na!}', $data['opt_value']);

	$data['opt_price'] = str_replace('{csvnc}', '{!na!}', $data['opt_price']);
	$data['opt_price'] = str_replace('{!na!}{!na!}', '{!na!}', $data['opt_price']);

	$data['opt_quant'] = str_replace('{csvnc}', '{!na!}', $data['opt_quant']);
	#$data['opt_quant'] = str_replace('{!na!}{!na!}', '{!na!}', $data['opt_quant']); 

	$data['opt_quant_d'] = str_replace('{csvnc}', '{!na!}', $data['opt_quant_d']);
	#$data['opt_quant_d'] = str_replace('{!na!}{!na!}', '{!na!}', $data['opt_quant_d']);

	//Получаем имена опций.
	$opt_array = explode('{next}', $data['opt_name']);
	$opt_value = explode('{next}', $data['opt_value']);
	$opt_price = explode('{next}', $data['opt_price']);
	$opt_quant = explode('{next}', $data['opt_quant']);
	$opt_quant_d = explode('{next}', $data['opt_quant_d']);
	$opt_data = explode('{next}', $data['opt_data']);

	$quant_d = $opt_quant_d[0];
	foreach ($opt_array as $key => $opt_name ) {
		$add = 1; #маркер добавления массива с опцией.

		//Проверяем если текст для имени опции. Если нет тогда берем по умолчанию.
		$opt_name = explode('{|}', $opt_name);
		if (empty($opt_name[0]) && empty($opt_name[1])) {
			$add = 0;
		}

		//если есть имя или id опции тогда добавляем ее в массив.
		if ($add) {
			$opts[$key]['name'] = $opt_name[0];
			$opts[$key]['opt_id'] = $opt_name[1];
			$opts[$key]['value_data'] = explode('{!na!}', $opt_value[$key]);
			$opts[$key]['price'] = explode('{!na!}', $opt_price[$key]);
			$opts[$key]['quant'] = explode('{!na!}', $opt_quant[$key]);
			if(empty($opt_quant_d[$key])){
				$opts[$key]['quant_d'] = $quant_d;
			}else{
				$opts[$key]['quant_d'] = $opt_quant_d[$key];
			}

			//преобразовываем дополнительные данные для опций.
			preg_match('#\{required_(.)?\}#', $opt_data[$key], $required);
			preg_match('#\{price_prefix_(.)?\}#', $opt_data[$key], $price_prefix);
			if (empty($required[1])){ $required[1] = 0; }
			if (empty($price_prefix[1])){ $price_prefix[1] = '+'; }
			$opts[$key]['required'] = $required[1];
			$opts[$key]['price_prefix'] = $price_prefix[1];

			//приводим опции их значения к единому стандарту, что бы на каждое значение были данные, нисмотря ни на что.
			//Приводим в соотвецтвие цену и количество к колву значений опции. Если нету значения опции удаляем ее цену и колво.
			$quant_d = (int)$opts[$key]['quant_d'];
			foreach ($opts[$key]['value_data'] as $key_v => $value) {
				$value = trim($value);

				if (!empty($value) || $value = '0') {

					$opts[$key]['value'][$key_v]['value_id'] = 0;
					$opts[$key]['value'][$key_v]['value'] = $value;

					if(empty($opts[$key]['price'][$key_v])){
						$opts[$key]['value'][$key_v]['price'] = '';
					}else{
						$opts[$key]['value'][$key_v]['price'] = str_replace(',', '.', str_replace(' ', '', $opts[$key]['price'][$key_v]));
					}
					//количество опции по умолчанию. 
					if(empty($opts[$key]['quant'][$key_v])){
						$opts[$key]['value'][$key_v]['quant'] = $quant_d;
					}else{
						$opts[$key]['value'][$key_v]['quant'] = (int)$opts[$key]['quant'][$key_v];
					}

					$opts[$key]['value'][$key_v]['price_prefix'] = $opts[$key]['price_prefix'];


				}
			}
			//Удаляем ненужные данные.
			unset($opts[$key]['value_data']);
			unset($opts[$key]['price']);
			unset($opts[$key]['quant']);
			unset($opts[$key]['price_prefix']);

		}

	}
	#$this->wtfarrey($opts);
	return $opts;
}

//Контроллер работы с опциями. Да будет так!
public function controlOption($data, $setting, $langs, $pr_id, $dn_id){

	//////////////////////////////////////////////////
	//Работа с Оption
	// 0 - Нет
  // 1 - Создать/Добавить/Обновить
	////////////////////////////////////////////////////
	#$this->wtfarrey($data);
	if ($setting['r_opt'] == 1){

		foreach ($data as $key => $opt) {

			//проверяем есть ли id опции, id выше имени опции.
			if (empty($opt['opt_id'])) {
				$opt['opt_id'] = $this->getOptId($opt['name']);
			}

			//Проверяем сново id опции, если 0 значит такой нет и нужно создать.
			if (empty($opt['opt_id'])) {
				$opt['opt_id'] = $this->addNewOpt($opt['name'], $langs, $dn_id);
			}

			//Проверяем что бы были значения в опциях.
			if(!empty($opt['value'])){
				//Проверяем есть ли в товаре такая опция.
				$product_option_id = $this->checkOptToProduct($pr_id, $opt['opt_id']);
				//если нет создаем запись.
				if (empty($product_option_id)) {
					$product_option_id = $this->addOptToProduct($opt, $setting, $langs, $pr_id, $dn_id);
				}

				//проверяем сушествуют ли значения опции.
				foreach ($opt['value'] as $key_v => $value) {

					$value = $this->getOptionValueId($opt['opt_id'], $value);

					//Если такого значения нет тогда создаем.
					if (empty($value['value_id'])) {
						#$opt['value'][$key_v] = $this->addNewOptionValue($opt['opt_id'], $value, $setting, $langs, $dn_id);
						$value = $this->addNewOptionValue($opt['opt_id'], $value, $setting, $langs, $dn_id);
					}

					//теперь проверяем если в товаре такая опция с таким значением.
					$pr_opt_value_id = $this->checkProductOptValue($opt, $value, $setting, $pr_id, $dn_id);
					//если такая запись есть обновляем ее.
					if (!empty($pr_opt_value_id)) {
						$this->doProductOptValue($opt, $value, $product_option_id, $pr_opt_value_id, $setting, $pr_id, $dn_id, $do='up');
					} else {
						//А если нету создаем.
						$this->doProductOptValue($opt, $value, $product_option_id, $pr_opt_value_id, $setting, $pr_id, $dn_id, $do='add');
					}
				}
			}
		}
	}
}

//Проверяем если в товаре такая опция
public function checkOptToProduct($pr_id, $opt_id){
	$product_option_id = 0;

	//Проверяем есть ли у товара эта опция
	$chack_opt = $this->db->query("SELECT * FROM `".DB_PREFIX."product_option` WHERE
		`product_id`=".(int)$pr_id." AND `option_id` =".(int)$opt_id);

	//если нету создаем.
	if ($chack_opt->num_rows > 0) {
		$product_option_id = $chack_opt->row['product_option_id'];
	}
	return $product_option_id;
}

//Добавляем опцию в товар
public function addOptToProduct($opt, $setting, $langs, $pr_id, $dn_id) {
	$product_option_id = 0;

	$this->db->query("INSERT INTO `".DB_PREFIX."product_option` SET
			`product_id`=".(int)$pr_id.",
			`option_id`=".(int)$opt['opt_id'].",
			`value`='',
			`required` = ".$this->db->escape($opt['required']));
	//Полуячаем id новой записи
	$product_option_id = $this->db->getLastId();

	//отправляем отчет в логи
	$log['opt_id'] = $opt['opt_id'];

	$this->log('addOptToProduct', $log, $dn_id);

	return $product_option_id;
}

//Проверяем есть ли запись в oc_product_option_value
public function checkProductOptValue($opt, $value, $setting, $pr_id, $dn_id){
	$product_option_value_id = 0;
	$sql = "SELECT * FROM `".DB_PREFIX."product_option_value` WHERE
		`product_id`=".(int)$pr_id." AND `option_id` =".(int)$opt['opt_id']." AND `option_value_id`=".(int)$value['value_id'];

	//теперь проверяем есть ли у этой опции такое значение что нам нужно.
	$chack_opt_value = $this->db->query("SELECT * FROM `".DB_PREFIX."product_option_value` WHERE
		`product_id`=".(int)$pr_id." AND `option_id` =".(int)$opt['opt_id']." AND `option_value_id`=".(int)$value['value_id']);

	if ($chack_opt_value->num_rows > 0) {

		$product_option_value_id = $chack_opt_value->row['product_option_value_id'];

	}

	return $product_option_value_id;
}

//Фкнция создания и обновления данных опции в товаре.
public function doProductOptValue($opt, $value, $product_option_id, $pr_opt_value_id, $setting, $pr_id, $dn_id, $do='add'){
	#$this->wtfarrey($opt);
	#проверяем что нужно. Обновить или создать.
	if($do == 'add') {

		$this->db->query("INSERT INTO ".DB_PREFIX."product_option_value SET
			product_option_id=".(int)$product_option_id.",
			product_id=".(int)$pr_id.",
			option_id=".(int)$opt['opt_id'].",
			option_value_id=".(int)$value['value_id'].",
			quantity=".(int)$value['quant'].",
			subtract=1,
			price=".(float)$value['price'].",
			price_prefix='".$this->db->escape($value['price_prefix'])."',
			points=0,
			points_prefix='+',
			weight='0.00',
			weight_prefix='+'");
		//отправляем отчет в логи
		$log['opt_id'] = (int)$opt['opt_id'];
		$log['value_id'] = (int)$value['value_id'];
		$log['pref'] = $this->db->escape($value['price_prefix']);
		$log['price'] = (float)$value['price'];
		$log['quant'] = (int)$value['quant'];

		$this->log('doProductOptValueAdd', $log, $dn_id);

	} elseif ($do == 'up') {

		$this->db->query("UPDATE ".DB_PREFIX."product_option_value SET
			product_option_id=".(int)$product_option_id.",
			product_id=".(int)$pr_id.",
			option_id=".(int)$opt['opt_id'].",
			option_value_id=".(int)$value['value_id'].",
			quantity=".(int)$value['quant'].",
			subtract=1,
			price=".(float)$value['price'].",
			price_prefix='".$this->db->escape($value['price_prefix'])."',
			points=0,
			points_prefix='+',
			weight='0.00',
			weight_prefix='+'
			WHERE product_option_value_id =".$pr_opt_value_id);
		//отправляем отчет в логи
		$log['opt_id'] = (int)$opt['opt_id'];
		$log['value_id'] = (int)$value['value_id'];
		$log['pref'] = $this->db->escape($value['price_prefix']);
		$log['price'] = (float)$value['price'];
		$log['quant'] = (int)$value['quant'];

		//Потому что опенкарт и опции это худшае что я когда либо видел
		//Я должен выполнять запрос на обновления соотсюда. С места гда этот запрос крайне не ожидано увидеть. 
		$this->db->query("UPDATE ".DB_PREFIX."product_option SET 
			required = ".(int)$opt['required']." 
			WHERE product_id = ".(int)$pr_id." AND option_id = ".(int)$opt['opt_id']);

		$this->log('doProductOptValueUp', $log, $dn_id);

	}

}

//Фунция добавление новых опций.
public function addNewOpt($opt_name, $langs, $dn_id){
	$opt_id = 0;
	$opt_name = trim($opt_name);
	//Создаем основую запись опции
	$this->db->query("INSERT INTO `".DB_PREFIX."option` SET `type`='select', `sort_order` = '0'");

	//Полуячаем id новой опции
	$opt_id = $this->db->getLastId();

	//Записываем дескрипшин опции
	foreach ($langs as $key => $lang) {
		$this->db->query("INSERT IGNORE INTO `".DB_PREFIX."option_description` SET
			`option_id` = ".(int)$opt_id.",
			`language_id` = '".(int)$lang['language_id']."',
			`name` = '".$this->db->escape($opt_name)."'");
	}

	//отправляем отчет в логи
	$log['opt_id'] = $opt_id;
	$log['opt_name'] = $opt_name;

	$this->log('LogAddNewOpt', $log, $dn_id);

	return $opt_id;
}

//Фунция получения id значения опции.
public function getOptionValueId($opt_id, $value){

	$value['value'] = trim($value['value']);
	$rows = $this->db->query("SELECT * FROM ".DB_PREFIX."option_value_description WHERE
		name ='".$this->db->escape($value['value'])."' AND option_id =".(int)$opt_id);

	if($rows->num_rows > 0){
		$value['value_id'] = $rows->row['option_value_id'];
	}

	#$this->wtfarrey($rows->row['option_id']);
	return $value;
}

//Фунция создания нового занчения опции
public function addNewOptionValue($opt_id, $value, $setting, $langs, $dn_id){

	$value['value'] = trim($value['value']);
	$this->db->query("INSERT INTO ".DB_PREFIX."option_value SET option_id =".(int)$opt_id.", sort_order=0");
	$value['value_id'] = $this->db->getLastId();

	//Создаем таблицу дескрипшин опции
	foreach ($langs as $key => $lang) {
		$this->db->query("INSERT IGNORE INTO ".DB_PREFIX."option_value_description SET
			option_value_id =".(int)$value['value_id'].",
			language_id =".(int)$lang['language_id'].",
			option_id =".(int)$opt_id.",
			name ='".$this->db->escape($value['value'])."'");
	}

	//отправляем отчет в логи
	$log['opt_id'] = $opt_id;
	$log['value_id'] = $value['value_id'];
	$log['value'] = $value['value'];

	$this->log('addNewOptionValue', $log, $dn_id);

	return $value;
}

//Фунция получения id опции.
public function getOptId($opt_name){
	$opt_id = 0;
	$opt_name = trim($opt_name);
	$opt = $this->db->query("SELECT * FROM `".DB_PREFIX."option_description` WHERE `name`='".$this->db->escape($opt_name)."'");
	if ($opt->num_rows > 0){
		$opt_id = $opt->row['option_id'];
	}

	#$this->wtfarrey($opt_id);
	return $opt_id;
}

//Подготавливаем данные Акцеонной цены.
public function madePriceSpec($data, $price){
	$price_spec = '';
	$data = explode('{|}', $data);
	//перебераем все варианты цены.
	foreach ($data as $value) {

		//приводим цену в соотвецтвующий вид
		if(empty($value)){
			$value = '0';
		}else{
			$value = (float)str_replace(' ', '', str_replace(',', '.', $value));
		}

		//выбираем какую цену оставить
		if (!empty($value)) {
			$price_spec = $value;
			break;
		}

	}

	//Цена скидки не должна совпадать с ценой товара. Если совпала зачишаем.
	if ($price_spec == $price) {
		$price_spec = '';
	}

	return $price_spec;
}

//фунция преобразования строки проверочных полей в массив.
public function madeGransPermitListToArr($data){

	//проверяем что бы строка не была пустой.
	if(!empty($data)){
		$data = htmlspecialchars_decode($data);
		$data = explode('{next}', $data);
		foreach($data as &$gran_arr){

			$gran_arr = explode('{!na!}', $gran_arr);
			$gran_arr = [
						        'switch' => $gran_arr[0],
						        'name' => $gran_arr[1],
						        'gran' => $gran_arr[2],
						        'operator' => $gran_arr[3],
						        'value' => $gran_arr[4],
						        'when_check' => $gran_arr[5]
					    		];
		}
	}else{
		//если строка пустая вернем пустой массив.
		$data = [];
	}
	#$this->wtfarrey($data);
	return $data;
}

//Проверка страницы на допуск к работе.
public function checkGransPermit($form, $setting, $dn_id){

	//1 - Добавлени Т | 2 - обновление товар | 3 - добавление и обновление Т |4 - Парсинг в csv | 5 - парсинг в кеш
	$data = [
					'1' => ['permit' =>1, 'log' => 'Код 808'], 
					'2' => ['permit' =>1, 'log' => 'Код 808'],
					'3' => ['permit' =>1, 'log' => 'Код 808'], 
					'4' => ['permit' =>1, 'log' => 'Код 808'], 
					'5' => ['permit' =>1, 'log' => 'Код 808'],
				];

	//проверяем что бы массив не был пустым.
	if(!empty($form['grans_permit_list'])){

		//проверяем все правила по очереди. 
		foreach($form['grans_permit_list'] as $rules){
			#$this->wtfarrey($rules);

			if(!empty($data[$rules['when_check']]['permit'])){

				//проверяем что бы правило было включено.
				if($rules['switch']){
					#Все типы правил. 
					#1 ->Не пустая | 2 ->Пустая | 3 ->Равна = | 4 ->Не равна != | 5 ->Содержит %значение% | 6 ->Не содержит %значение%

					//Если граница пустая отключаем загрузку страницы
					if($rules['operator'] == 1){

						if( empty($rules['gran']) && $rules['gran'] != '0' ){

							$data[$rules['when_check']]['permit'] = 0;
							$data[$rules['when_check']]['log'] = ' Правило допуска ['.$rules['name'].'] Пустое';

						}
					//Если граница НЕ пустая отключаем загрузку страницы
					}elseif($rules['operator'] == 2){

						if( !empty($rules['gran']) || $rules['gran'] == '0' ){

							$data[$rules['when_check']]['permit'] = 0;
							$data[$rules['when_check']]['log'] = ' Правило допуска ['.$rules['name'].'] Не пустое';

						}
					//Если граница НЕ равна значениею отменяем загрузку
					}elseif($rules['operator'] == 3){

						if( $rules['gran'] != $rules['value'] ){

							$data[$rules['when_check']]['permit'] = 0;
							$data[$rules['when_check']]['log'] = ' Правило допуска ['.$rules['name'].'] Не равно ['.$rules['value'].']';

						}
					//Если граница равна значению то отменяем загзку
					}elseif($rules['operator'] == 4){

						if( $rules['gran'] == $rules['value'] ){

							$data[$rules['when_check']]['permit'] = 0;
							$data[$rules['when_check']]['log'] = ' Правило допуска ['.$rules['name'].'] Равно ['.$rules['value'].']';

						}
					//Если граница не содержит значение отменяем загрузку
					}elseif($rules['operator'] == 5){

						$value = preg_quote($rules['value'], '#');
						if(!preg_match('#(.*)'.$value.'(.*)#s', $rules['gran'])){
						
							$data[$rules['when_check']]['permit'] = 0;
							$data[$rules['when_check']]['log'] = ' Правило допуска ['.$rules['name'].'] Не содержит значение ['.$rules['value'].']';
						
						}
					//Если содержит значение отменяем
					}elseif($rules['operator'] == 6){

						$value = preg_quote($rules['value'], '#');
						if(preg_match('#(.*)'.$value.'(.*)#s', $rules['gran'])){
							
							$data[$rules['when_check']]['permit'] = 0;
							$data[$rules['when_check']]['log'] = ' Правило допуска ['.$rules['name'].'] Содержит значение ['.$rules['value'].']';
						
						}
					//проверка по регулярному выражению. 
					}elseif($rules['operator'] == 7){

						//Отлавливаем регулярные вырежения в правилах поиск замена
						if(preg_match('#^\{reg\[(.*)\]\}$#', $rules['value'], $reg)){
							//Вернем в жизнь правило.
							$reg = htmlspecialchars_decode($reg[1]);
							//проверка правила, если правило false значит отбрасываем эту страницу.
							if(!preg_match($reg, $rules['gran'])){
								$data[$rules['when_check']]['permit'] = 0;
								$data[$rules['when_check']]['log'] = ' Правило допуска ['.$rules['name'].'] Не прошло проверку регулярного выражения '.$rules['value'];
							}

						}else{
							$data[$rules['when_check']]['permit'] = 0;
							$data[$rules['when_check']]['log'] = ' Правило допуска ['.$rules['name'].'] Содержит неправильно записанное регулярное выражение!!!';
						}
					}
				}
			}
		}
	}

	//проверяем границу добавление и обновление если она содержит 0 тоже самое добавляем В add и up
	if($data[3]['permit'] == '0'){
		$data[1]['permit'] = 0;
		$data[1]['log'] = $data[3]['log'];
		$data[2]['permit'] = 0;
		$data[2]['log'] = $data[3]['log'];
	}
	#$this->wtfarrey($form['grans_permit_list']);
	#$this->wtfarrey($setting);
	#$this->wtfarrey($data);
	return $data;
}

public function startParsToIm($dn_id){
	//Получам дополнительные данные из настроек.
	$setting = $this->getSettingToProduct($dn_id);

	if($setting['sid'] == 'sku' && $setting['r_sku'] == 1){
  	$this->answjs('finish','ПАРСИНГ ОСТАНОВЛЕН : Нельзя обновлять значение которое является идентификатором товара. Измените действие в поле Артикул (sku)');
	}
	if($setting['sid'] == 'name' && $setting['r_name'] == 1){
	  $this->answjs('finish','ПАРСИНГ ОСТАНОВЛЕН : Нельзя обновлять значение которое является идентификатором товара. Измените действие в поле Название');
	}

	//Получаем списк неспарсенных ссылок.
	$pars_url = $this->getUrlToPars($dn_id, $setting['link_list'], $setting['link_error']);

	//Проверяем закончился ли парсинг.
	if(empty($pars_url['links'])){
		//Подсчет ссылок
		$totals = $pars_url['total'];
		$answ['progress'] = 100;
		$answ['clink'] = ['link_scan_count' => $pars_url['total'], 'link_count' => $pars_url['queue'],];

    $this->answjs('finish','Парсинг закончился, ссылок больше нет﻿',$answ);
	}else{

		//Блак многопоточности. берем нужное количество ссылок.
		$urls = [];
		foreach($pars_url['links'] as $key => $url){
			if($key < $setting['thread']){ $urls[] = $url['link']; } else { break; }
		}

		//делаем мульти запрос
		$datas = $this->multiCurl($urls, $dn_id);

		//перебераем данные с мулти запроса.
		foreach($datas as $key => $data){

			//производим зяпись лога курл, и паролельно проверяем нужно ли делать дальнейшую работу.
  		$curl_error = $this->sentLogMultiCurl($data ,$dn_id);

			#помечаем ссылку как отсканированная
    	$this->db->query("UPDATE ". DB_PREFIX ."pars_link SET scan=0, error='".$curl_error['http_code']."' WHERE link='".$data['url']."' AND dn_id=".$dn_id);

  		//если пришла ошибка заканчиваем эту итерацию и переходим к следующей.
  		if($curl_error['error']){ 
  			continue;
  		}

			//Ссылка
			$link = $data['url'];
			//Прасим данные
			$form = $this->preparinDataToStore($data, $dn_id);

			//Получаем разрешения на действия.
			if(!empty($setting['grans_permit'])){
				$permit_grans = $this->checkGransPermit($form, $setting, $dn_id);

				//проверяем массив допуска и сравниваем с выбранным действием. 
				if($setting['action'] != 3 && empty($permit_grans[$setting['action']]['permit'])){ 
					$this->log('NoGranPermit', $permit_grans[$setting['action']]['log'], $dn_id);
					continue; 
				}
			}

			//Получаем разрешения на действия.
			$permit = $this->checkProduct($form, $setting, $link, $dn_id);

			//Проверка выбора действия./// И проверка допуска страницы ///////////////// 
			// Допуск страниц $permit_grans[1]['permit']
			// 1 - Добавлени Т | 2 - обновление товар | 3 - добавление и обновление Т |4 - Парсинг в csv | 5 - парсинг в кеш
			//
			// Действия с товаром $setting['action']
			// 1 -Добавлять | 2 - Обновлять | 3 - Добавлять и обновлять
			///////////////////////////////////////////////////////////////////////////
			if($setting['action'] == 1){

				//провека допуска к добавлению товара
				if($permit['add']['permit'] == 1){
					$this->addProduct($form, $link, $setting, $dn_id);
				}else{
					$log = ['sid' => $setting['sid'],	'sid_value' => $form[$setting['sid']],];
					$this->log('addProductIsTrue', $log, $dn_id);
				}

			}elseif($setting['action'] == 2){

				//провека допуска к обновлению товара
				if($permit['up']['permit'] == 1){
					$this->updateProduct($form, $link, $setting, $dn_id, $permit['up']['pr_id']);
				}else{
					$log = [ 'sid' => $setting['sid'],	'sid_value' => $form[$setting['sid']], 'link' => $link ];
					#$this->wtfarrey($log);
					$this->log('NoFindProductToUpdate', $log, $dn_id);
				}

			}elseif($setting['action'] == 3){

				if($permit['add']['permit'] == 1){

					//проверка допуска страницы к добавлению товара, и включена ли проверка допуска
					if(!isset($permit_grans) || !empty($permit_grans[1]['permit'])){ 

						//провека допуска на добавление товара
						$this->addProduct($form, $link, $setting, $dn_id);

					}else{
						$this->log('NoGranPermit', $permit_grans[1]['log'], $dn_id);
					}

				}elseif($permit['up']['permit'] == 1){

					//проверка допуска страницы к обновлению товара, и включена ли проверка допуска
					if(!isset($permit_grans) || !empty($permit_grans[2]['permit'])){ 

						//проверка на обновление товара
						$this->updateProduct($form, $link, $setting, $dn_id, $permit['up']['pr_id']);

					}else{
						$this->log('NoGranPermit', $permit_grans[2]['log'], $dn_id);
					}

				}
			}
		}

    #считаем процент для прогрес бара
    $scan = ($pars_url['total']-$pars_url['queue']);
    $progress = $scan/($pars_url['total']/100);
    $answ['progress'] = $progress;
    $answ['clink'] = ['link_scan_count' => $scan, 'link_count' => $pars_url['queue'],];
    #пауза парсинга
    $this->timeSleep($setting['pars_pause']);
    $this->answjs('go','Производится парсинг',$answ);
	}
}

//загрузка фото в описании.
public function dwImgToDesc($desc, $url, $dn_id){
	#$desc = htmlspecialchars_decode($desc);

	preg_match_all('#\{img\}(.*?)>#m', $desc, $imgs_tmp);
	#$this->wtfarrey($imgs_tmp);
	

	//Если массив не пустой значит есть фото в описании. 
	if(!empty($imgs_tmp)){

		//определяем доме для относительной ссылки.
		$domain = parse_url($url);
		#$this->wtfarrey($domain);

		#Массив для отправки на скачивание фото в мультипоточном режиме.
		$img_arr = [];

		//перебираем каждый элемент массива для преобразования.
		foreach($imgs_tmp[0] as $key_img => $var){

			//Удаляем лишние
			$var = preg_replace('#\{img\}(.*?)src="#', '', $var);
			$var = preg_replace('#"(.*)#', '', $var);
			#$this->wtfarrey($var);
			$imgs[$key_img]['short'] = $var;
			if($var != false){
				//Добавлем нужные элементы к ссылке.
				if($var[0] == '/' && $var[1] != '/'){
					$var = $domain['scheme'].'://'.$domain['host'].$var;

				}elseif($var[0] == '/' && $var[1] == '/'){
					$var = str_ireplace('//', $domain['scheme'].'://',$var);
				}
				$imgs[$key_img]['full'] = $var;
				$img_arr[] = $var;
			}

		}

		//Если массив на скачивание не пустой тогда качаем все фото.
		if(!empty($img_arr)){

			$img_path = $this->dwImagToProduct($dn_id, $img_arr, $dir='description', $under=1);
			#для удобства переносим результат в обыший массив.
			foreach($img_path as $key_path => $path){
				#$imgs[$key_path]['path'] = $path;
				//Добавляем недостающую часть.
				$path = HTTPS_CATALOG.'image/'.$path;
				//заменяем в описании текст с фото донора на наши фото.
				$desc = preg_replace('#\{img\}(.*?)'.preg_quote($imgs[$key_path]['short'], '#').'(.*?)>#m', '<img alt="" src="'.$path.'" width="100%">', $desc, 1);
				//текст ниже если вы хотите сохранить параметры фото в описании
				#$desc = preg_replace('#\{img\}#', '<img', $desc);
				#$desc = preg_replace('#src="'.preg_quote($imgs[$key_path]['short'], '#').'"#', 'src="'.$path.'"', $desc, 1);

			}

		}
		#$this->wtfarrey($imgs);
		#$this->wtfarrey($img_arr);
		#$this->wtfarrey($img_path);
	}
	#$this->wtfarrey($desc);
	return $desc;
}

//Запускаем парсинг одной ссылки.
public function preparinDataToStore($data, $dn_id){
	//Получаем настройки
	$params = $this->getParsParam($dn_id);

	$form = $this->getPrSetup($dn_id);
	#$this->wtfarrey($form);
	//Получаем код страницы.
	$html = $data['content'];

	//Получаем $url
	$url = $data['url'];

	//Перебераем все поля товара.
	foreach($form as $mark => $pdata){
		//А здесь все границы парсинга что были созданы.
		foreach($params as $param){

			//если какая то граница совпала начинаем делать ветер.
			if(strpos($pdata, '{gran_'.$param['id'].'}') !== false){

				//парсим границу.
				$gvar = $this->parsParam($html, $param['id']);
				#$this->wtfarrey($gvar);
				//Если первый тип границы, не повторяющийся. Тогда замешаем значения в лоб.
				if($param['type'] == 1){
					//применяем поск замена.
					$gvar = $this->findReplace($gvar, $param['id']);
					$form[$mark] = str_replace('{gran_'.$param['id'].'}', $gvar, $form[$mark]);
					#$this->wtfarrey($form[$mark]);
				}
				#$this->wtfarrey($form);
				//Если повторяющиеся границы парсинга тогда перебераем массивы и составляем строку из массива, и замешаем.
				if($param['type'] == 2){
					$arr = '';
					foreach($gvar as $gv_key => $gstr){
					if ($gv_key != 0) { $na = '{!na!}'; } else { $na = ''; }
					//применяем поск замена.
					$gstr = $this->findReplace($gstr, $param['id']);

						if($mark == 'img'){
							$arr .= '{!na!}'.$gstr;
						}elseif($mark == 'cat'){
							$arr .= '{!na!}'.$gstr;
						}elseif($mark == 'attr'){
							$arr .= $gstr.'{!na!}';
						}elseif($mark == 'opt_name'){
							$arr .= $gstr.'{!na!}';
						}elseif($mark == 'opt_value'){
							$arr .= $gstr.'{!na!}';
						}elseif($mark == 'opt_price'){
							$arr .= $gstr.'{!na!}';
						}elseif($mark == 'opt_quant'){
							$arr .= $gstr.'{!na!}';
						}else{
							$arr .= $gstr;
						}
					}
					#записываем в строку результат.
					$form[$mark] = str_replace('{gran_'.$param['id'].'}', $arr, $form[$mark]);
				}

			}
		}
	}
	#$this->wtfarrey($form);
	//Финальная обработка данных в массиве.

	//Ниже вырезаем спец маяки из данных где должно быть одно значение, а использовали повторяющиеся границы парсинга.
	$form['model'] = substr(trim(str_replace('{!na!}','',$form['model'])), 0, 64);
	$form['sku'] = substr(trim(str_replace('{!na!}','',$form['sku'])), 0, 64);
	$form['name'] = substr(trim(str_replace('{!na!}','',$form['name'])), 0, 255);

	//Работаем над ценой товара
	$form['price'] = str_replace('{!na!}','',$form['price']);
	$form['price'] = $this->madeLogicalMathem($form['price'], 'int');

	//работаем с ценой скидки.
	$form['price_spec'] = str_replace('{!na!}','',$form['price_spec']);
	$form['price_spec'] = $this->madePriceSpec($form['price_spec'], $form['price']);

	$form['quant'] = str_replace('{!na!}','',$form['quant']);
	$form['quant'] = $this->madeLogicalMathem($form['quant'], 'int');

	$form['manufac'] = trim(str_replace('{!na!}','',$form['manufac']));
	$form['des'] = trim(str_replace('{!na!}','',$form['des']));
	
	#Разное
	$form['upc'] = substr(trim(str_replace('{!na!}','',$form['upc'])), 0, 64);
	$form['ean'] = substr(trim(str_replace('{!na!}','',$form['ean'])), 0, 64);
	$form['jan'] = substr(trim(str_replace('{!na!}','',$form['jan'])), 0, 64);
	$form['isbn'] = substr(trim(str_replace('{!na!}','',$form['isbn'])), 0, 64);
	$form['mpn'] = substr(trim(str_replace('{!na!}','',$form['mpn'])), 0, 64);
	$form['location'] = substr(trim(str_replace('{!na!}','',$form['location'])), 0, 128);
	
	$form['minimum'] = (int)str_replace('{!na!}','',$form['minimum']); 
	if(empty($form['minimum'])) {$form['minimum'] = 1;}
	
	$form['subtract'] = (int)str_replace('{!na!}','',$form['subtract']);

	$form['length'] = (float)str_replace('{!na!}','',$form['length']);
	if(empty($form['length'])) {$form['length'] = '0.00';}

	$form['width'] = (float)str_replace('{!na!}','',$form['width']);
	if(empty($form['width'])) {$form['width'] = '0.00';}

	$form['height'] = (float)str_replace('{!na!}','',$form['height']);
	if(empty($form['height'])) {$form['height'] = '0.00';}

	$form['length_class_id'] = trim(str_replace('{!na!}','',$form['length_class_id']));
	
	$form['weight'] = str_replace('{!na!}','',$form['weight']);
	if(empty($form['weight'])) {$form['weight'] = '0.00';}

	$form['weight_class_id'] = (int)str_replace('{!na!}','',$form['weight_class_id']);
	$form['status'] = (int)str_replace('{!na!}','',$form['status']);
	$form['sort_order'] = (int)str_replace('{!na!}','',$form['sort_order']);

	#Товар
	$form['seo_url'] = str_replace('{!na!}','',$form['seo_url']);
	$form['seo_h1'] = str_replace('{!na!}','',$form['seo_h1']);
	$form['seo_title'] = str_replace('{!na!}','',$form['seo_title']);
	$form['seo_desc'] = str_replace('{!na!}','',$form['seo_desc']);
	$form['seo_keyw'] = str_replace('{!na!}','',$form['seo_keyw']);
	#Категории
	$form['cat_seo_url'] = str_replace('{!na!}','',$form['cat_seo_url']);
	$form['cat_seo_h1'] = str_replace('{!na!}','',$form['cat_seo_h1']);
	$form['cat_seo_title'] = str_replace('{!na!}','',$form['cat_seo_title']);
	$form['cat_seo_desc'] = str_replace('{!na!}','',$form['cat_seo_desc']);
	$form['cat_seo_keyw'] = str_replace('{!na!}','',$form['cat_seo_keyw']);
	#Производители
	$form['manuf_seo_url'] = str_replace('{!na!}','',$form['manuf_seo_url']);
	$form['manuf_seo_h1'] = str_replace('{!na!}','',$form['manuf_seo_h1']);
	$form['manuf_seo_title'] = str_replace('{!na!}','',$form['manuf_seo_title']);
	$form['manuf_seo_desc'] = str_replace('{!na!}','',$form['manuf_seo_desc']);
	$form['manuf_seo_keyw'] = str_replace('{!na!}','',$form['manuf_seo_keyw']);

	//Преобразования категорий
	$form['cat'] = $this->madeCatArrey($form['cat']);
	//преобразовывает фото для парсинга.
	$form['img'] = $this->madeImgArrey($form['img'], $url);
	//Преобразовываем атрибуты.
	$form['attr'] = $this->madeAttrArrey($form['attr']);
	//преобразуем опции
	$form['opts'] = $this->madeOption($form);
	//преобразовываем проверочные данные в массив
	$form['grans_permit_list'] = $this->madeGransPermitListToArr($form['grans_permit_list']);

	#$this->wtfarrey($form);
	return $form;
}

public function addProduct($data, $link, $setting, $dn_id){
	#$this->wtfarrey($data);
	#$this->wtfarrey($setting);
	//получаем списко используемых языков
	$langs = $this->getLang($setting);
	//Получаем выбранный магазин.
	$stores = $this->getStore($setting);

	//Ниже поля по умолчанию.
	if(empty($data['model'])){ $data['model'] = '';	}
	if(empty($data['sku'])){ $data['sku'] = '';	}
	if(empty($data['name'])){	$data['name'] = ''; }
	if(empty($data['price'])){
		$data['price'] = '0';
	}else{
		$data['price'] = (float)str_replace(' ', '', str_replace(',', '.', $data['price']));
	}
	if(empty($data['price_spec'])){
		$data['price_spec'] = '0';
	}else{
		$data['price_spec'] = (float)str_replace(' ', '', str_replace(',', '.', $data['price_spec']));
	}

	if(empty($data['des'])){
		$data['des'] = '';
		if(!empty($data['des_d'])){
			$data['des'] = $data['des_d'];
		}
	}

	if(empty($data['cat'])){	$data['cat'] = []; }
	if(empty($data['img'])){	$data['img'] = []; }
	if(empty($data['attr'])){	$data['attr'] = [];	}

	//Дописываем нужные поля. Пока они не парсятся но на будушее будут обрабатыватся.
	if(empty($data['tax_class_id'])){	$data['tax_class_id'] = 0; }
	if(empty($setting['r_status_zero'])){	$data['stock_status_id'] = 7;	} else { $data['stock_status_id'] = $setting['r_status_zero'];}
	if(empty($data['date_available'])){	$data['date_available'] = date("Y-m-d"); }
	if(empty($data['shipping'])){	$data['shipping'] = 1; }
	
	//количество товара
	if(empty($data['quant'])){
		if($data['quant'] != '0'){
			if(empty($data['quant_d'])){
				$data['quant'] = 0;
			}else{
				$data['quant'] = (int)$data['quant_d'];
			}
		}

	}else{

		$data['quant'] = (int)$data['quant'];
		if($data['quant'] == 0){
			if(empty($data['quant_d'])){
				$data['quant'] = 0;
			}else{
				$data['quant'] = (int)$data['quant_d'];
			}
		}
	}

	//определяем статус товара
	$data['status'] = $this->getProductStatus($data, $setting);

	$permit = 1;
	//Если по правилам модель создается по умолчанию то так и делаем.
	if($setting['r_model'] == 1){
		$model = $this->db->query("SELECT MAX(`product_id`) as lid FROM " . DB_PREFIX . "product");
		$data['model'] = $model->row['lid']+1;
	}elseif($setting['r_model'] == 2){
		if(empty($data['model'])){
			$permit = 0;
			$log = '';
			$this->log('NoParsModel', $log, $dn_id);
		}
	}

	if($permit == 1){

		//Здесь начинаем собирать все кости в кучу.
		//========================================

		//Создаем массив с данными для логов добавления товара.
		$log[0] = ['sid'=>$setting['sid'], 'sid_value'=>$data[$setting['sid']]];
		
		/////////////////////////////////////////////////
		//Работа с Производителями.
		// 0 - Ничего | 1-Создавать/Добавлять/Обновлять| 2 - Только обновлять
		//////////////////////////////////////////////////
		if($setting['r_manufac'] == 0 || $setting['r_manufac'] == 2){

			$data['manufacturer_id'] = 0;

		}elseif($setting['r_manufac'] == 1){

			if(empty($data['manufac'])){
				//По умолчанию
				$data['manufacturer_id'] = $data['manufac_d'];
			}else{

				#Получаем id мануфактуры.
				$manuf_id = $this->getIdManuf($data['manufac']);

				#если нету такой тогда создаем. И получаем id этой мануфак
				if($manuf_id > 0){
					$data['manufacturer_id'] = $manuf_id;
				}else{
					#Создаем производителя. И получаем в ответ id этого производителя.
					$data['manufacturer_id'] = $this->addManuf($data, $langs, $setting, $stores, $dn_id);
				}
			}

		}

		////////////////////////////////Заморозка загруки false//////////////////
		if(1){

		//Главный запрос на добавления товара.
		$this->db->query("INSERT INTO " . DB_PREFIX . "product
			SET model = '" . $this->db->escape($data['model']) . "',
			sku = '" . $this->db->escape($data['sku']) . "',
			quantity = '" . (int)$data['quant'] . "',
			stock_status_id = '" . (int)$data['stock_status_id'] . "',
			date_available = '" . $this->db->escape($data['date_available']) . "',
			manufacturer_id = '" . (int)$data['manufacturer_id'] . "',
			shipping = '" . (int)$data['shipping'] . "',
			price = '" . (float)$data['price'] . "',
			tax_class_id = '" . (int)$data['tax_class_id'] . "',
			upc = '" . $this->db->escape($data['upc']) . "',
			ean = '" . $this->db->escape($data['ean']) . "',
			jan = '" . $this->db->escape($data['jan']) . "',
			isbn = '" . $this->db->escape($data['isbn']) . "',
			mpn = '" . $this->db->escape($data['mpn']) . "',
			location = '" . $this->db->escape($data['location']) . "',
			minimum = " . (int)$data['minimum'] . ",
			subtract = ".(int)$data['subtract'].",
			length = ".(float)$data['length'].",
			width = ".(float)$data['width'].",
			height = ".(float)$data['height'].",
			weight = ".(float)$data['weight'].",
			weight_class_id = '" . (int)$data['weight_class_id'] . "',
			length_class_id = '" . (int)$data['length_class_id'] . "',
			sort_order = '" . (int)$data['sort_order'] . "',
			dn_id = '" . (int)$dn_id . "',
			status = '" . (int)$data['status'] . "',
			date_added = NOW(),
			date_modified = NOW()");

			//Получаем id нового товара.
			$pr_id = $this->db->getLastId();

			//Добаляем товар в магазины
			foreach ($stores as $store) {
				$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$pr_id . "', store_id = '" . $store['store_id']. "'");
			}

			//Записываем значения в лог файл
			$log[0]['pr_id'] = $pr_id;
			$log[] = ['name'=> 'Код товара [model]', 'value'=> $data['model']];
			$log[] = ['name'=> 'Артикул [sku]', 'value'=> $data['sku']];
			$log[] = ['name'=> 'Количество', 'value'=> (int)$data['quant']];
			$log[] = ['name'=> 'Производиетль id', 'value'=> (int)$data['manufacturer_id']];
			$log[] = ['name'=> 'Цена', 'value'=> (float)$data['price']];

			$log[] = ['name'=> 'UPС', 'value'=> $data['upc']];
			$log[] = ['name'=> 'EAN', 'value'=> $data['ean']];
			$log[] = ['name'=> 'JAN', 'value'=> $data['jan']];
			$log[] = ['name'=> 'ISBN', 'value'=> $data['isbn']];
			$log[] = ['name'=> 'MPN', 'value'=> $data['mpn']];
			$log[] = ['name'=> 'Location', 'value'=> $data['location']];
			$log[] = ['name'=> 'Минимальный заказ', 'value'=> (int)$data['minimum']];
			$log[] = ['name'=> 'Вычитать со склада', 'value'=> (int)$data['subtract']];
			$log[] = ['name'=> 'Длина', 'value'=> (float)$data['length']];
			$log[] = ['name'=> 'Ширина', 'value'=> (float)$data['width']];
			$log[] = ['name'=> 'Высота', 'value'=> (float)$data['height']];
			$log[] = ['name'=> 'Единица длины', 'value'=> (int)$data['length_class_id']];
			$log[] = ['name'=> 'Вес', 'value'=> (float)$data['weight']];
			$log[] = ['name'=> 'Единица веса', 'value'=> (int)$data['weight_class_id']];
			$log[] = ['name'=> 'Сортировка', 'value'=> (int)$data['sort_order'] ];
			$log[] = ['name'=> 'Статус', 'value'=> (int)$data['status']];

		#Контроль создалась основная часть товара.
		if($pr_id){

			//Таблицы для описания незнаю буду ли с ними работать но добавлю в основу модуля.
			if(empty($data['tag'])){
				$data['tag'] = '';
			}
			if(empty($data['meta_title'])){
				$data['meta_title'] = '';
			}
			if(empty($data['meta_h1'])){
				$data['meta_h1'] = '';
			}
			if(empty($data['meta_description'])){
				$data['meta_description'] = '';
			}
			if(empty($data['meta_keyword'])){
				$data['meta_keyword'] = '';
			}

			//////////////////////////////////////////////////
			//Работа с MATA ДАННЫМИ
			// 0 - Незаполнять
			// 1 - По SEO шаблону
			////////////////////////////////////////////////////
			if($setting['r_made_meta'] == 1){

				if(!empty($data['seo_title'])){
					$data['meta_title'] = $data['seo_title'];
				}
				if(!empty($data['seo_desc'])){
					$data['meta_description'] = $data['seo_desc'];
				}
				if(!empty($data['seo_keyw'])){
					$data['meta_keyword'] = $data['seo_keyw'];
				}
				if(!empty($data['seo_h1'])){
					$data['meta_h1'] = $data['seo_h1'];
				}
			}

			//обработка фото описаний.
			$data['des'] = $this->dwImgToDesc($data['des'], $link, $dn_id);

			//Проверяем версию движка для правильного заполнения.
			$mh1 = '';
			if($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'ocstore3'){
				$mh1 = ",meta_h1='".$this->db->escape($data['meta_h1'])."'";
			}

			//Добавление описания
			foreach ($langs as $key => $lang) {
				//Составляе строку.
				$sql = "INSERT INTO " . DB_PREFIX . "product_description
				SET product_id = '" . (int)$pr_id . "',
				language_id = '" . (int)$lang['language_id'] . "',
				name = '" . $this->db->escape($data['name']) . "',
				description = '" . $this->db->escape($data['des']) . "',
				tag = '" . $this->db->escape($data['tag']) . "',
				meta_title = '" . $this->db->escape($data['meta_title']) . "',
				meta_description = '" . $this->db->escape($data['meta_description']) . "',
				meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'".$mh1;
				//Запрос
				$this->db->query($sql);
				}

				//логи
				$log[] = ['name'=>'Название','value'=>$this->db->escape($data['name'])];

			//////////////////////////////////////////////////
			//Работа с КАТЕГОРИЯМИ
			// 0 - Не создавать новые | Не обновлять [Не рекомендую, товар получит категорию с id = 0]
			// 1 - Создать новые | Привязать товар
			// 2 - Привязать товар | Не создавать новые
			////////////////////////////////////////////////////
			$data['cats_id'][0] = 0;
			if($setting['r_cat'] == 0){
				#если выбран вариант не создавать категории и грузить товар в 0
				$data['cats_id'][0] = 0;
				//Категория по умолчанию
				if($data['cats_id'][0] == 0 && $data['cats_id'][0]!=0){
					$data['cats_id'][0] = $data['cats_id'][0];
				}

			}elseif($setting['r_cat'] == 1){
				#Создать категории и привазять товар.

				//проверяем массив категорий.
				if(!empty($data['cat'])){

					//проверяем есть ли такая котегория и если есть возврашем ее id
					$data['cats_id'] = $this->getCategorysId($data['cat']);

					//если такая категория есть тогда оставляем его id для товара, если нет. Отправляемся создавать категории.
					if($data['cats_id'][0] == 0){
						$this->addCat($data, $setting, $langs, $stores, $dn_id);
						$data['cats_id'] = $this->getCategorysId($data['cat']);
					}

				}elseif($data['cat_d'] != 0){
					$data['cats_id'][0] = $data['cat_d'];
				}

			}elseif($setting['r_cat'] == 2){ //если добавлять товар только в сушествуюшие категории.
				//проверяем массив категорий.
				if(!empty($data['cat'])){

					//проверяем есть ли такая котегория и если есть возврашем ее id
					$data['cats_id'] = $this->getCategorysId($data['cat']);
					//Категория по умолчанию
					if($data['cats_id'][0] == 0 and $data['cat_d']!=0){
						$data['cats_id'][0] = $data['cat_d'];
					}

				}elseif($data['cat_d']!=0){
					$data['cats_id'][0] = $data['cat_d'];
				}

			}

			//Добавляем товар в нужную категорию.
			$log_cat = $this->addProdToCat($data['cats_id'], $pr_id, $setting);
			$log[] = ['name' =>'Категории','value'=>$log_cat];
			$this->log('addProduct', $log, $dn_id);
			
			//////////////////////////////////////////////////
			//Работа с акционными ценами.
			//////////////////////////////////////////////////
			//Если акционная цена не равна нулю значит будем ее добавлять в магазин
			if ($data['price_spec'] != 0) {
				$this->addPriceSpecToProduct($data['price_spec'], $setting, $pr_id, $dn_id);
			}

			//////////////////////////////////////////////////
			//Работа с фото.
			// ПРАВИЛО ДИРЕКТОРИЙ| 0 - не создавать папки и не раскладывать фото | 1- Создать директории и разложить фото
			// УСЛОВИЯ | 0-НЕТ|1-Добавлять при содании т|2-Добавлять при создании и обновлении товара т|3-Обновлять
			//////////////////////////////////////////////////
			#Массив с сылками путями к фото для базы.
			$data['img_path'] = [];
			#if($setting['r_img']==1 || $setting['r_img']==2 || $setting['r_img']==3){
			if($setting['r_img'] != '0'){
				if(empty($data['img'])){

					if(!empty($data['img_d'])){
						$data['img'][0] = $data['img_d'];
						$data['img_path'] = $this->dwImagToProduct($dn_id, $data['img'], $data['img_dir'], $setting['r_img_dir']);
					}else{
						$logs['pr_id'] = $pr_id;
						$this->log('fotoNotData', $logs, $dn_id);
					}

				}else{
					$data['img_path'] = $this->dwImagToProduct($dn_id, $data['img'], $data['img_dir'], $setting['r_img_dir']);
				}
			}


			//Добавление Фото
			if(!empty($data['img_path'])){
				#Добавление главного фото
				$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['img_path'][0]) . "' WHERE product_id = '" . (int)$pr_id . "'");

				#Добавление доп фото.
				foreach ($data['img_path'] as $key => $image) {
					if($key == 0) continue;
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$pr_id . "', image = '" . $this->db->escape($image) . "', sort_order = '0'");
				}
			}


			//////////////////////////////////////////////////
			//Работа с АТРИБУТАМИ
			// 0 - Нет
			// 1 - Создать/Добавить/Обновить
			// 2 - Добавить/Обновить
			// 3 - Обновить
			// 4 - Удалить и создать заново
			////////////////////////////////////////////////////
			if($setting['r_attr'] == 0){
				$data['attr'] = [];

			}elseif($setting['r_attr'] == 1 || $setting['r_attr'] == 4){
				#Создаем атрибуты и добавляем в товар.

				#Проверяем сушествует атрибут или нет.
				foreach($data['attr'] as $attr){

					$attr['id'] = $this->getIdAttr($attr[0]);
					#Если нету тогда создаем.
					if($attr['id'] == 0){
						$attr['id'] = $this->addAttr($attr[0], $langs, $setting, $dn_id);
						//Если после создания атрибут есть тогда записываем его в товар. Если нет проходим дальше.
						if($attr['id'] != 0){
							$this->addAttrToProduct($pr_id, $attr, $langs, $dn_id);
						}

					}else{
						#Если такой атрибут найден тогда присвяеваем его товару.
						$this->addAttrToProduct($pr_id, $attr, $langs, $dn_id);
					}

				}

			}elseif($setting['r_attr'] == 2){
				#Проверяем есть ли такой атрибу если да добавляем в товар.
				foreach($data['attr'] as $attr){
					$attr['id'] = $this->getIdAttr($attr[0]);
					//Если есть такой атрибут добавляем его в товар. Если нет пропускаем.
					if($attr['id'] != 0){
						$this->addAttrToProduct($pr_id, $attr, $langs, $dn_id);
					}

				}

			}
			//////////////////////////////////////////////////
			//Работа с Оption
			// 0 - Нет
      // 1 - Создать/Добавить/Обновить
			////////////////////////////////////////////////////
			$this->controlOption($data['opts'], $setting, $langs, $pr_id, $dn_id);

			//////////////////////////////////////////////////
			//Работа с SEO_URL
			// 0 - Незаполнять
			// 1 - Создать из имени товара
			// 2 - Создать по шаблону заполненому на вкладке SEO
			////////////////////////////////////////////////////
			if($setting['r_made_url'] == 1){

				//Получаем юрл из имени.
				if(!empty($data['name'])){
					$pr_url = $this->madeUrl($data['name']);

					//Записываем url
					$do = ['where'=>'pr','what'=>'add'];
					$this->addSeoUrl($pr_url, $pr_id, $setting, $langs, $stores, $dn_id, $do);
				}else{
					$logs['name'] = 'product name';
					$this->log('badUrl', $logs, $dn_id);
				}

			}elseif($setting['r_made_url'] == 2){

				if(!empty($data['seo_url'])){
					//Получаем юрл из имени.
					$pr_url = $this->madeUrl($data['seo_url']);

					//Записываем url
					$do = ['where'=>'pr','what'=>'add'];
					$this->addSeoUrl($pr_url, $pr_id, $setting, $langs, $stores, $dn_id, $do);
				}else{
					$logs['name'] = 'seo_url';
					$this->log('badUrl', $logs, $dn_id);
				}
			}

		}#Контроль над $pr_id

		}#Заморозка добавления товара.
	}#Конец permit
}

//Одна из самых страшных фунций :)
public function updateProduct($data, $link, $setting, $dn_id, $pr_id=0){

	if(empty($data['model'])){ $data['model'] = '';	}
	if(empty($data['sku'])){ $data['sku'] = '';	}
	if(empty($data['name'])){	$data['name'] = ''; }

	if(empty($data['price'])){
		$data['price'] = '0';
	}else{
		$data['price'] = (float)str_replace(' ', '', str_replace(',', '.', $data['price']));
	}

	if(empty($data['price_spec'])){
		$data['price_spec'] = '0';
	}else{
		$data['price_spec'] = (float)str_replace(' ', '', str_replace(',', '.', $data['price_spec']));
	}

	if(empty($data['des'])){ $data['des'] = ''; }
	if(empty($data['cat'])){ $data['cat'] = []; }
	if(empty($data['img'])){ $data['img'] = []; }
	if(empty($data['attr'])){ $data['attr'] = []; }

	//Таблицы для описания незнаю буду ли с ними работать но добавлю в основу модуля.
	if(empty($data['tag'])){ $data['tag'] = ''; }
	if(empty($data['meta_title'])){ $data['meta_title'] = ''; }
	if(empty($data['meta_h1'])){ $data['meta_h1'] = '';}
	if(empty($data['meta_description'])){ $data['meta_description'] = ''; }
	if(empty($data['meta_keyword'])){ $data['meta_keyword'] = ''; }
	if(empty($setting['r_status_zero'])){	$data['stock_status_id'] = 7;	} else { $data['stock_status_id'] = $setting['r_status_zero'];}
	
	//количество товара
	if(empty($data['quant'])){
		if($data['quant'] != '0'){
			if(empty($data['quant_d'])){
				$data['quant'] = 0;
			}else{
				$data['quant'] = (int)$data['quant_d'];
			}
		}

	}else{

		$data['quant'] = (int)$data['quant'];
		if($data['quant'] == 0){
			if(empty($data['quant_d'])){
				$data['quant'] = 0;
			}else{
				$data['quant'] = (int)$data['quant_d'];
			}
		}
	}
	
	//определяем статус товара
	$data['status'] = $this->getProductStatus($data, $setting);

	//Получаем выбранный магазин.
	$stores = $this->getStore($setting);
	//получаем списко используемых языков
	$langs = $this->getLang($setting);

	//Товар найден в базе дальше начинаем смотреть что нам обновить.
	if($pr_id > 0){
		#К обновлению допушен начнем составлять логи.
		$log[] = ['pr_id'=>$pr_id, 'sid'=>$setting['sid'], 'sid_value'=>$data[$setting['sid']]];
		#Начинаем разбор данных на обновленние.

		///////////////////////////////////////
		//Разное
		// 0 - Нет | 1 - Обновить
		///////////////////////////////////////
		$set_product = 'SET';
		//SKU	|| 0 - Нет | 1 - Обновить
		if($setting['r_sku'] == 1 && !empty($data['sku'])){
			$set_product = $set_product." sku='".$this->db->escape($data['sku'])."',";
			$log[] = ['name'=>'Артикул (sku)', 'value'=>$data['sku']];
		}

		if($setting['r_upc']){
			$set_product = $set_product." upc='".$this->db->escape($data['upc'])."',";
			$log[] = ['name'=> 'UPС', 'value'=> $data['upc']];
		}
		if($setting['r_ean']){
			$set_product = $set_product." ean='".$this->db->escape($data['ean'])."',";
			$log[] = ['name'=> 'EAN', 'value'=> $data['ean']];
		}
		if($setting['r_jan']){
			$set_product = $set_product." jan='".$this->db->escape($data['jan'])."',";
			$log[] = ['name'=> 'JAN', 'value'=> $data['jan']];
		}
		if($setting['r_isbn']){
			$set_product = $set_product." isbn='".$this->db->escape($data['isbn'])."',";
			$log[] = ['name'=> 'ISBN', 'value'=> $data['isbn']];
		}
		if($setting['r_mpn']){
			$set_product = $set_product." mpn='".$this->db->escape($data['mpn'])."',";
			$log[] = ['name'=> 'MPN', 'value'=> $data['mpn']];
		}
		if($setting['r_location']){
			$set_product = $set_product." location='".$this->db->escape($data['location'])."',";
			$log[] = ['name'=> 'Location', 'value'=> $data['location']];
		}
		if($setting['r_minimum']){
			$set_product = $set_product." minimum='".(int)$data['minimum']."',";
			$log[] = ['name'=> 'Минимальный заказ', 'value'=> (int)$data['minimum']];
		}
		if($setting['r_subtract']){
			$set_product = $set_product." subtract='".(int)$data['subtract']."',";
			$log[] = ['name'=> 'Вычитать со склада', 'value'=> (int)$data['subtract']];
		}
		if($setting['r_length']){
			$set_product = $set_product." length='".(float)$data['length']."',";
			$log[] = ['name'=> 'Длина', 'value'=> (float)$data['length']];
		}
		if($setting['r_width']){
			$set_product = $set_product." width='".(float)$data['width']."',";
			$log[] = ['name'=> 'Ширина', 'value'=> (float)$data['width']];
		}
		if($setting['r_height']){
			$set_product = $set_product." height='".(float)$data['height']."',";
			$log[] = ['name'=> 'Высота', 'value'=> (float)$data['height']];
		}
		if($setting['r_length_class_id']){
			$set_product = $set_product." length_class_id='".(int)$data['length_class_id']."',";
			$log[] = ['name'=> 'Единица длины', 'value'=> (int)$data['length_class_id']];
		}
		if($setting['r_weight']){
			$set_product = $set_product." weight='".(float)$data['weight']."',";
			$log[] = ['name'=> 'Вес', 'value'=> (float)$data['weight']];
		}
		if($setting['r_weight_class_id']){
			$set_product = $set_product." weight_class_id='".(int)$data['weight_class_id']."',";
			$log[] = ['name'=> 'Единица веса', 'value'=> (int)$data['weight_class_id']];
		}
		if($setting['r_status']){
			$set_product = $set_product." status='".(int)$data['status']."',";
			$log[] = ['name'=> 'Статус', 'value'=> (int)$data['status']];
		}
		if($setting['r_sort_order']){
			$set_product = $set_product." sort_order='".(int)$data['sort_order']."',";
			$log[] = ['name'=> 'Сортировка', 'value'=> (int)$data['sort_order'] ];
		}

		$set_product = $set_product." dn_id = ".(int)$dn_id.", date_modified = NOW()";

		//Обязательный запрос на обновление. 
		$up_sku = $this->db->query("UPDATE " . DB_PREFIX . "product ".$set_product." WHERE `product_id`=".(int)$pr_id);

		#$this->wtfarrey($set_product);
		///////////////////////////////////////
		//Название
		// 0 - Нет | 1 - Обновить
		///////////////////////////////////////
		if($setting['r_name'] == 1 && !empty($data['name'])){
			//////////////////////////////////////////////////
			//Работа с MЕTA ДАННЫМИ
			// 0 - Незаполнять
			// 1 - По SEO шаблону
			////////////////////////////////////////////////////
			if($setting['r_made_meta'] == 1){

				if(!empty($data['seo_title'])){
					$data['meta_title'] = $data['seo_title'];
				}
				if(!empty($data['seo_desc'])){
					$data['meta_description'] = $data['seo_desc'];
				}
				if(!empty($data['seo_keyw'])){
					$data['meta_keyword'] = $data['seo_keyw'];
				}
				if(!empty($data['seo_h1'])){
					$data['meta_h1'] = $data['seo_h1'];
				}

				//Проверяем версию движка для правильного заполнения.
				$mh1 = '';
				if($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'ocstore3'){
					$mh1 = ",meta_h1='".$this->db->escape($data['meta_h1'])."'";
				}

				//Составляе строку.
				foreach ($langs as $lang) {
					//проверяем есть ли запись в этом языке.
					$sql_desc = $this->db->query("SELECT * FROM ".DB_PREFIX."product_description WHERE `product_id`=".(int)$pr_id." AND language_id=".(int)$lang['language_id']);

					#Если есть запись обновляем, если нету создаем.
					if($sql_desc->num_rows > 0) {
						$sql = "UPDATE " . DB_PREFIX . "product_description SET
						name = '" . $this->db->escape($data['name']) . "',
						meta_title = '" . $this->db->escape($data['meta_title']) . "',
						meta_description = '" . $this->db->escape($data['meta_description']) . "',
						meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'".$mh1."
						WHERE `product_id`=".(int)$pr_id." AND language_id=".(int)$lang['language_id'];
						//Запрос
						$up_name = $this->db->query($sql);

					}else{

						$sql = "INSERT INTO " . DB_PREFIX . "product_description SET
						product_id=".(int)$pr_id.",
						language_id=".(int)$lang['language_id'].",
						name = '" . $this->db->escape($data['name']) . "',
						meta_title = '" . $this->db->escape($data['meta_title']) . "',
						meta_description = '" . $this->db->escape($data['meta_description']) . "',
						meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'".$mh1;
						//Запрос
						$up_name = $this->db->query($sql);
					}


				}
			}else{
				foreach ($langs as $lang) {

					//проверяем есть ли запись в этом языке.
					$sql_desc = $this->db->query("SELECT * FROM ".DB_PREFIX."product_description WHERE `product_id`=".(int)$pr_id." AND language_id=".(int)$lang['language_id']);

					#Если есть запись обновляем, если нету создаем.
					if($sql_desc->num_rows > 0) {
						$up_name = $this->db->query("UPDATE " . DB_PREFIX . "product_description SET
							`name`='".$this->db->escape($data['name'])."'
							WHERE `product_id`=".(int)$pr_id." AND language_id=".(int)$lang['language_id']);
					}else{
						$up_name = $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET
							`product_id`=".(int)$pr_id.",
							`language_id`=".(int)$lang['language_id'].",
							`name`='".$this->db->escape($data['name'])."'");
					}

				}
		}

			//////////////////////////////////////////////////
			//Работа с SEO_URL
			// 0 - Незаполнять
			// 1 - Создать из имени товара
			// 2 - Создать по шаблону заполненому на вкладке SEO
			////////////////////////////////////////////////////
			if($setting['r_made_url'] == 1){

				//Получаем юрл из имени.
				if(!empty($data['name'])){
					$pr_url = $this->madeUrl($data['name']);

					//Записываем url
					$do = ['where'=>'pr','what'=>'up'];
					$this->addSeoUrl($pr_url, $pr_id, $setting, $langs, $stores, $dn_id, $do);
				}else{
					$logs['name'] = 'product name';
					$this->log('badUrl', $logs, $dn_id);
				}

			}elseif($setting['r_made_url'] == 2){

				if(!empty($data['seo_url'])){
					//Получаем юрл из имени.
					$pr_url = $this->madeUrl($data['seo_url']);

					//Записываем url
					$do = ['where'=>'pr','what'=>'up'];
					$this->addSeoUrl($pr_url, $pr_id, $setting, $langs, $stores, $dn_id, $do);
				}else{
					$logs['name'] = 'seo_url';
					$this->log('badUrl', $logs, $dn_id);
				}
			}


			if($up_name) $log[] = ['name'=>'Название', 'value'=>$data['name']];
		}
		///////////////////////////////////////
		//Цена
		// 0 - Нет | 1 - Обновить
		///////////////////////////////////////

		if($setting['r_price'] == 1 && !empty($data['price'])){
			$up_price = $this->db->query("UPDATE " . DB_PREFIX . "product SET `price`='".(float)$data['price']."' WHERE `product_id`=".(int)$pr_id);

			if($up_price) $log[] = ['name'=>'Цена', 'value'=>$data['price']];

			//Акционная цена
			if ($data['price_spec'] != 0) {
				$this->addPriceSpecToProduct($data['price_spec'], $setting, $pr_id, $dn_id);
			}else{
				$this->delPriceSpecToProduct($data['price_spec'], $setting, $pr_id, $dn_id);
			}

		}
		///////////////////////////////////////
		//Количество
		// 0 - Нет | 1 - Обновить
		///////////////////////////////////////
		if($setting['r_quant'] == 1){
			$up_quant = $this->db->query("UPDATE " . DB_PREFIX . "product SET
				`quantity`='".(int)$data['quant']."',
				`stock_status_id`='".(int)$data['stock_status_id']."'
				WHERE `product_id`=".(int)$pr_id);

			if($up_quant) $log[] = ['name'=>'Количество', 'value'=>$data['quant']];
		}
		///////////////////////////////////////
		//Производитель
		// 0 - Ничего | 1-Создавать/Добавлять/Обновлять| 2 - Только обновлять
		///////////////////////////////////////
		if($setting['r_manufac'] == 1 && !empty($data['manufac'])){

			$manuf_id = $this->getIdManuf($data['manufac']);

			if($manuf_id == 0){
				//Создаем производителя.
				$manuf_id = $this->addManuf($data, $langs, $setting, $stores, $dn_id);
				#Проверяем создание.
				if($manuf_id != 0){
					$up_manuf = $this->db->query("UPDATE " . DB_PREFIX . "product SET `manufacturer_id`='".(int)$manuf_id."' WHERE `product_id`=".(int)$pr_id);

					if($up_manuf) $log[] = ['name'=>'Производитель id', 'value'=>$manuf_id];
				}

			}else{
				$up_manuf = $this->db->query("UPDATE " . DB_PREFIX . "product SET `manufacturer_id`='".(int)$manuf_id."' WHERE `product_id`=".(int)$pr_id);

				if($up_manuf) $log[] = ['name'=>'Производитель id', 'value'=>$manuf_id];
			}

		}elseif($setting['r_manufac'] == 2 && !empty($data['manufac'])){

			$manuf_id = $this->getIdManuf($data['manufac']);
			if($manuf_id > 0){
				$up_manuf = $this->db->query("UPDATE " . DB_PREFIX . "product SET `manufacturer_id`='".(int)$manuf_id."' WHERE `product_id`=".(int)$pr_id);

				if($up_manuf) $log[] = ['name'=>'Производитель id', 'value'=>$manuf_id];
			}

		}
		///////////////////////////////////////
		//Описание
		// 0 - Нет | 1 - Обновить
		///////////////////////////////////////
		if($setting['r_des'] == 1 && !empty($data['des'])){

			//обработка фото описаний.
			$data['des'] = $this->dwImgToDesc($data['des'], $link, $dn_id);

			foreach ($langs as $lang) {

				//проверяем есть ли запись в этом языке.
				$sql_desc = $this->db->query("SELECT * FROM ".DB_PREFIX."product_description WHERE `product_id`=".(int)$pr_id." AND language_id=".(int)$lang['language_id']);

				#Если есть запись обновляем, если нету создаем.
				if($sql_desc->num_rows > 0) {

					$up_des = $this->db->query("UPDATE " . DB_PREFIX . "product_description SET `description`='".$this->db->escape($data['des'])."' WHERE `product_id`=".(int)$pr_id." AND language_id=".(int)$lang['language_id']);

				}else{

					$up_des = $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET
						`product_id`=".(int)$pr_id.",
						`language_id`=".(int)$lang['language_id'].",
						`description`='".$this->db->escape($data['des'])."'");
				}

			}

			if($up_des) $log[] = ['name'=>'Описание', 'value'=>'{описание в логи не пишется}'];
		}

		///////////////////////////////////////
		//Категории
		// 0 - Не создавать новые | Не обновлять [Не рекомендую, товар получит категорию с id = 0]
		// 1 - Создать новые | Привязать товар
		// 2 - Привязать товар | Не создавать новые
		///////////////////////////////////////
		if($setting['r_cat'] == 1){

			//Формируем имя для логов. В дальнейшем нужно вырезать. Зачем эти ништяки если их не читают.
			$cat_way = implode('->', $data['cat']);
			//проверяем есть ли такая котегория и если есть возврашем ее id
			$data['cats_id'] = $this->getCategorysId($data['cat']);

			if($data['cats_id'][0] == 0){

				$this->addCat($data, $setting, $langs, $stores, $dn_id);
				$data['cats_id'] = $this->getCategorysId($data['cat']);

				if($data['cats_id'][0] > 0){

					//Вместо обновления, мы удаляем записи, и создаем заново.
					$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id`=".(int)$pr_id);

					//Добавляем товар в нужную категорию.
					$this->addProdToCat($data['cats_id'], $pr_id, $setting);

					$log[] = ['name'=>'Категория id = '.implode(',',$data['cats_id']).' Адрес', 'value'=>$cat_way];
				}

			}else{

				//Вместо обновления, мы удаляем записи, и создаем заново.
				$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id`=".(int)$pr_id);

				//Добавляем товар в нужную категорию.
				$this->addProdToCat($data['cats_id'], $pr_id, $setting);

				$log[] = ['name'=>'Категория id = '.implode(',',$data['cats_id']).' Адрес', 'value'=>$cat_way];
			}

		}elseif($setting['r_cat'] == 2){
			//Формируем имя для логов. В дальнейшем нужно вырезать. Зачем эти ништяки если их не читают.
			$cat_way = implode('->', $data['cat']);

			//проверяем есть ли такая котегория и если есть возврашем ИХ!!!!!! ID
			$data['cats_id'] = $this->getCategorysId($data['cat']);

			if($data['cats_id'][0] != 0){

				//Вместо обновления, мы удаляем записи, и создаем заново.
				$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id`=".(int)$pr_id);

				//Добавляем товар в нужную категорию.
				$this->addProdToCat($data['cats_id'], $pr_id, $setting);

				$log[] = ['name'=>'Категория id = '.implode(',',$data['cats_id']).' Адрес', 'value'=>$cat_way];

			} else {

				$data['cats_id'][0] = $data['cat_d'];
				//Вместо обновления, мы удаляем записи, и создаем заново.
				$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id`=".(int)$pr_id);

				//Добавляем товар в нужную категорию.
				$this->addProdToCat($data['cats_id'], $pr_id, $setting);
				$log[] = ['name'=>'Категория id = '.$data['cats_id'][0].' ', 'value'=>'Категория по умолчанию'];
			}

		}
		//////////////////////////////////////
		//Фото
		// 0 - Нет
		// 1 - Добавлять при создании товара
		// 2 - Добавлять при обновлении товара
		// 3 - Обновлять [Заменит все фото у товара][Изображения не удаляются с сервера!]
		// 4 - Обновлять и удалить старые [Внимание!!! Старые фото товара будут удалены с сервера]
		///////////////////////////////////////
		if($setting['r_img'] == 2 && !empty($data['img'])){
			#Добавлять при обновлении товара
			//начинаем перебор массива с фото
			$data['img_path'] = $this->dwImagToProduct($dn_id, $data['img'], $data['img_dir'], $setting['r_img_dir']);

			//если нету главного фото то добавляем его в товар.
			$check_main_img = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id=".(int)$pr_id);

			if(empty($check_main_img->row['image']) && !empty($data['img_path'])){
				$up_img = $this->db->query("UPDATE ".DB_PREFIX."product SET image='".$this->db->escape($data['img_path'][0])."' WHERE product_id =".(int)$pr_id);

				if($up_img) $log[] = ['name'=>'Главное изображение', 'value' => HTTP_CATALOG.'image/'.$data['img_path'][0]];
				//Удаляем главное фото из массива.
				unset($data['img_path'][0]);
			}

			//Добавлем доп фото.
			foreach($data['img_path'] as $image){
				$up_img = $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$pr_id . "', image = '" . $this->db->escape($image) . "', sort_order = '0'");

				if($up_img) $log[] = ['name'=>'Дополнительные изображения', 'value' => HTTP_CATALOG.'image/'.$image];
			}

		}elseif($setting['r_img'] == 3 && !empty($data['img'])){
			#Обновлять [Заменит все фото у товара][Изображения не удаляюстся с сервера!]
			$data['img_path'] = $this->dwImagToProduct($dn_id, $data['img'], $data['img_dir'], $setting['r_img_dir']);
			//если массив пустой тогда ничего не удаляем, нету смысла.
			if(!empty($data['img_path'])){
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id=".(int)$pr_id);

				//Добавляем главное фото
				$up_img_main = $this->db->query("UPDATE ".DB_PREFIX."product SET image='".$this->db->escape($data['img_path'][0])."' WHERE product_id ='". (int)$pr_id ."'");

				if($up_img_main) $log[] = ['name'=>'Главное изображение', 'value' => HTTP_CATALOG.'image/'.$data['img_path'][0]];
				//Удаляем главное фото из массива.
				unset($data['img_path'][0]);

				//Добавлем доп фото.
				foreach($data['img_path'] as $image){
					$up_img = $this->db->query("INSERT INTO ".DB_PREFIX ."product_image SET product_id ='".(int)$pr_id."', image ='".$this->db->escape($image)."', sort_order = '0'");

					if($up_img) $log[] = ['name'=>'Дополнительные изображения', 'value' => HTTP_CATALOG.'image/'.$image];
				}
			}
		}elseif($setting['r_img'] == 4 && !empty($data['img'])){
			#Обновлять и удалить старые [Внимание!!! Старые фото товара будут удалены с сервера]

			$data['img_path'] = $this->dwImagToProduct($dn_id, $data['img'], $data['img_dir'], $setting['r_img_dir']);
			//если массив пустой тогда ничего не удаляем, нету смысла.
			if(!empty($data['img_path'])){
				//для удаления фото товара применяем фунцию из редактора товаров ;) 
				$this->toolDelProductsImg($data, $pr_id, $dn_id);
				$log[] = ['name'=>'Удалены старые изображения фото с сервера. И загружены новые', 'value' => '=>'];
				#$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id=".(int)$pr_id);

				//Добавляем главное фото
				$up_img_main = $this->db->query("UPDATE ".DB_PREFIX."product SET image='".$this->db->escape($data['img_path'][0])."' WHERE product_id ='". (int)$pr_id ."'");

				if($up_img_main) $log[] = ['name'=>'Главное изображение', 'value' => HTTP_CATALOG.'image/'.$data['img_path'][0]];
				//Удаляем главное фото из массива.
				unset($data['img_path'][0]);

				//Добавлем доп фото.
				foreach($data['img_path'] as $image){
					$up_img = $this->db->query("INSERT INTO ".DB_PREFIX ."product_image SET product_id ='".(int)$pr_id."', image ='".$this->db->escape($image)."', sort_order = '0'");

					if($up_img) $log[] = ['name'=>'Дополнительные изображения', 'value' => HTTP_CATALOG.'image/'.$image];
				}
			}
		}
		//////////////////////////////////////
		//Атрибуты
		// 0 - Нет
		// 1 - Создать/Добавить/Обновить
		// 2 - Добавить/Обновить
		// 3 - Обновить [Только существующие в товаре]
		// 4 - Удалить из товара создать заново
		///////////////////////////////////////
		if($setting['r_attr'] == 1 && !empty($data['attr'])){
			#Создаем атрибуты и добавляем в товар.
			#Проверяем сушествует атрибут или нет.
			foreach($data['attr'] as $attr){

				$attr['id'] = $this->getIdAttr($attr[0]);

				#Если нету тогда создаем.
				if($attr['id'] == 0){

					$attr['id'] = $this->addAttr($attr[0], $langs, $setting, $dn_id);
					//Если после создания атрибут есть тогда записываем его в товар. Если нет проходим дальше.
					if($attr['id'] != 0){
						$this->addAttrToProduct($pr_id, $attr, $langs, $dn_id);

						$log[] = ['name'=>'Атрибут добавлен в товар '.$attr[0], 'value'=>$attr[1]];
					}

				}elseif($attr['id'] > 0){

					//Проверяем есть ли в товере такой атрибут.
					$check_attr = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_attribute`
						WHERE `product_id`=".(int)$pr_id." AND `attribute_id`=".(int)$attr['id']);

					if($check_attr->num_rows > 0){

						//Значит такой атрибут есть и нужно его обновить.
						$attr[1] = trim($attr[1]);
						$this->db->query("UPDATE `" . DB_PREFIX . "product_attribute` SET `text`='".$this->db->escape($attr[1])."' WHERE `product_id`=".(int)$pr_id." AND `attribute_id`=".(int)$attr['id']);
						$log[] = ['name'=>'Атрибут обновлен в товаре '.$attr[0], 'value'=>$attr[1]];

					}else{

						//если нет тогда добавить его в товар.
						$this->addAttrToProduct($pr_id, $attr, $langs, $dn_id);
						$log[] = ['name'=>'Атрибут добавлен в товар '.$attr[0], 'value'=>$attr[1]];
					}
				}
			}

		}elseif($setting['r_attr'] == 2 && !empty($data['attr'])){
			#добавляем в товар только сушествующие или обновляем только сушествующие
			#Проверяем сушествует атрибут или нет.
			foreach($data['attr'] as $attr){

				$attr['id'] = $this->getIdAttr($attr[0]);

				#Если сушествует тогда проверяем если в товаре.
				if($attr['id'] > 0){

					//Проверяем есть ли в товере такой атрибут.
					$check_attr = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_attribute`
						WHERE `product_id`=".(int)$pr_id." AND `attribute_id`=".(int)$attr['id']);

					if($check_attr->num_rows > 0){

						//Значит такой атрибут есть и нужно его обновить.
						$attr[1] = trim($attr[1]);
						$this->db->query("UPDATE `" . DB_PREFIX . "product_attribute` SET `text`='".$this->db->escape($attr[1])."' WHERE `product_id`=".(int)$pr_id." AND `attribute_id`=".(int)$attr['id']);
						$log[] = ['name'=>'Атрибут обновлен в товаре '.$attr[0], 'value'=>$attr[1]];

					}else{

						//если нет тогда добавить его в товар.
						$this->addAttrToProduct($pr_id, $attr, $langs, $dn_id);
						$log[] = ['name'=>'Атрибут добавлен в товар '.$attr[0], 'value'=>$attr[1]];
					}
				}
			}

		}elseif($setting['r_attr'] == 3 && !empty($data['attr'])){
			#Обновить только сушествующие
			#Проверяем сушествует атрибут или нет.
			foreach($data['attr'] as $attr){

				$attr['id'] = $this->getIdAttr($attr[0]);

				#Если сушествует тогда проверяем если в товаре.
				if($attr['id'] > 0){

					//Проверяем есть ли в товере такой атрибут.
					$check_attr = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_attribute`
						WHERE `product_id`=".(int)$pr_id." AND `attribute_id`=".(int)$attr['id']);

					if($check_attr->num_rows > 0){

						//Значит такой атрибут есть и нужно его обновить.
						$attr[1] = trim($attr[1]);
						$this->db->query("UPDATE `" . DB_PREFIX . "product_attribute` SET `text`='".$this->db->escape($attr[1])."' WHERE `product_id`=".(int)$pr_id." AND `attribute_id`=".(int)$attr['id']);
						$log[] = ['name'=>'Атрибут обновлен в товаре '.$attr[0], 'value'=>$attr[1]];

					}
				}
			}

		}elseif($setting['r_attr'] == 4 && !empty($data['attr'])){
			#Удаляем из товар все атрибуты и записываем заново.

			#Сначала удаляем все атрибуты из товара.
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_attribute` WHERE `product_id`=".(int)$pr_id);

			#Проверяем сушествует атрибут или нет.
			foreach($data['attr'] as $attr){

				$attr['id'] = $this->getIdAttr($attr[0]);

				#Если нету тогда создаем.
				if($attr['id'] == 0){

					$attr['id'] = $this->addAttr($attr[0], $langs, $setting, $dn_id);
					//Если после создания атрибут есть тогда записываем его в товар. Если нет проходим дальше.
					if($attr['id'] != 0){
						$this->addAttrToProduct($pr_id, $attr, $langs, $dn_id);

						$log[] = ['name'=>'Атрибут добавлен в товар '.$attr[0], 'value'=>$attr[1]];
					}

				}elseif($attr['id'] > 0){
					//если нет тогда добавить его в товар.
					$this->addAttrToProduct($pr_id, $attr, $langs, $dn_id);
					$log[] = ['name'=>'Атрибут добавлен в товар '.$attr[0], 'value'=>$attr[1]];
				}
			}
		}

		//Отправляем отчет об обновлении.
		$this->log('UpdateProduct', $log, $dn_id);

		//////////////////////////////////////////////////
		//Работа с Оption
		// 0 - Нет
    // 1 - Создать/Добавить/Обновить
		////////////////////////////////////////////////////
		$this->controlOption($data['opts'], $setting, $langs, $pr_id, $dn_id);
	}

}

############################################################################################
############################################################################################
#						Страница пред просмотра парсинга в им.
############################################################################################
############################################################################################

public function getFormShowProduct($dn_id){
	$links = $this->db->query("SELECT * FROM " . DB_PREFIX . "pars_link WHERE `dn_id`=".(int)$dn_id." ORDER BY id ASC LIMIT 0, 250");
	$links = $links->rows;
	return $links;
}

//Контролер пред просмотра товара.
public function goShowToIm($url, $dn_id){
	//Выполняем запрос на пред просмотр.
	$url = str_replace('&amp;', '&', $url);
	$urls[] = $url;
	$datas = $this->multiCurl($urls, $dn_id);
	#$this->wtfarrey($datas[$url]);
	//пишем логи, но не проверяем ошибку она не нужна в пред просмотре.
	$curl_error = $this->sentLogMultiCurl($datas[$url], $dn_id);
	//Берем тот массив с мульти запроса у которого ключ равен ссылке. 
	$form = $this->preparinDataToStore($datas[$url], $dn_id);
	
	//информация о допусках

	//Получам дополнительные данные из настроек.
	$setting = $this->getSettingToProduct($dn_id);
	
	//Получаем разрешения на действия.
	if(!empty($setting['grans_permit'])){
		
		$form['permit_grans'] = $this->checkGransPermit($form, $setting, $dn_id);
		//проверяем допуски
		if( empty($form['permit_grans'][1]['permit']) || empty($form['permit_grans'][2]['permit']) || empty($form['permit_grans'][3]['permit'])){
			$form['permit_grans_text'] = 'ВНИМАНИЕ!!! Страница не прошла все допуски, подробнее в отладочной информации.';
		}

	}
	
	#$this->wtfarrey($setting);

	if($setting['r_model'] == 1){ 
		$form['model'] = 'По умолчанию (id товара)';
	}

	//Грубое применение значений по умолчанию.
	//количество товара
	if(empty($form['quant'])){
		if($form['quant'] != '0'){
			if(empty($form['quant_d'])){
				$form['quant'] = 0;
			}else{
				$form['quant'] = (int)$form['quant_d'];
			}
		}

	}else{

		$form['quant'] = (int)$form['quant'];
		if($form['quant'] == 0){
			if(empty($form['quant_d'])){
				$form['quant'] = 0;
			}else{
				$form['quant'] = (int)$form['quant_d'];
			}
		}
	}

	if(empty($form['des'])){
		$form['des'] = $form['des_d'];
	}
	if(empty($form['img'])){
		$form['img'][] = $form['img_d'];
	}
	if(empty($form['price'])){
		$form['price'] = 0;
	}else{
		$form['price'] = (float)str_replace(',','.', str_replace(' ', '', $form['price']));
	}
	#$this->wtfarrey($form['img']);
	$form['img_info'] = $form['img'];
	//преобразование и парсинг фото для пред посмотра.
	$form['img'] = $this->madeImgShow($form['img'], $dn_id);

	array_walk_recursive($form, array($this, 'htmlview'));

	$form['debug_text'] = $this->madeDebugIfo($form, $url);

	#$this->wtfarrey($form);
	return $form;
}

//Пред просмотр формирование отладочной информации.
public function madeDebugIfo($data, $url){
	$text = [];

	if(!empty($data)){
		$text['pr']['url'] = ['name'=>'Ссылка на товар который просматриваете', 'row'=>1, 'text'=>$url];
		$text['pr']['model'] = ['name'=>'Код товара', 'row'=>1, 'text'=>$data['model']];
		$text['pr']['sku'] = ['name'=>'Артикул', 'row'=>1, 'text'=>$data['sku']];
		$text['pr']['name'] = ['name'=>'Название', 'row'=>1, 'text'=>$data['name']];
		$text['pr']['price'] = ['name'=>'Цена', 'row'=>1, 'text'=>$data['price']];
		$text['pr']['price_spec'] = ['name'=>'Акционная цена', 'row'=>1, 'text'=>$data['price_spec']];
		$text['pr']['quant'] = ['name'=>'Количество', 'row'=>1, 'text'=>$data['quant']];
		$text['pr']['manufac'] = ['name'=>'Производитель', 'row'=>1, 'text'=>$data['manufac']];
		$text['pr']['des'] = ['name'=>'Описание', 'row'=>10, 'text'=>$data['des']];
		$text['pr']['cat'] = ['name'=>'Категории', 'row'=>5, 'text'=>''];
		$text['pr']['img'] = ['name'=>'Изображения', 'row'=>10, 'text'=>''];
		$text['pr']['attr'] = ['name'=>'Атрибуты', 'row'=>10, 'text'=>''];


		$text['seo']['seo_url'] = ['name'=>'SEO URL Ссылка на товар', 'row'=>1, 'text'=>$this->madeUrl($data['seo_url'])];
    $text['seo']['seo_h1'] = ['name'=>'HTML-тег H1 товара', 'row'=>1, 'text'=>$data['seo_h1']];
    $text['seo']['seo_title'] = ['name'=>'HTML-тег Title товара', 'row'=>2, 'text'=>$data['seo_title']];
    $text['seo']['seo_desc'] = ['name'=>'Мета-тег Description товара', 'row'=>5, 'text'=>$data['seo_desc']];
    $text['seo']['seo_keyw'] = ['name'=>'Мета-тег Keywords товара', 'row'=>5, 'text'=>$data['seo_keyw']];

    $text['seo']['cat_seo_url'] = ['name'=>'SEO URL Ссылка категории', 'row'=>1, 'text'=>$this->madeUrl($data['cat_seo_url'])];
    $text['seo']['cat_seo_h1'] = ['name'=>'HTML-тег H1 категории', 'row'=>1, 'text'=>$data['cat_seo_h1']];
    $text['seo']['cat_seo_title'] = ['name'=>'HTML-тег Title категории', 'row'=>2, 'text'=>$data['cat_seo_title']];
    $text['seo']['cat_seo_desc'] = ['name'=>'Мета-тег Description категории', 'row'=>5, 'text'=>$data['cat_seo_desc']];
    $text['seo']['cat_seo_keyw'] = ['name'=>'Мета-тег Keywords категории', 'row'=>5, 'text'=>$data['cat_seo_keyw']];

    $text['seo']['manuf_seo_url'] = ['name'=>'SEO URL Ссылка производителя', 'row'=>1, 'text'=>$this->madeUrl($data['manuf_seo_url'])];
    $text['seo']['manuf_seo_h1'] = ['name'=>'HTML-тег H1 производителя', 'row'=>1, 'text'=>$data['manuf_seo_h1']];
    $text['seo']['manuf_seo_title'] = ['name'=>'HTML-тег Title производителя', 'row'=>2, 'text'=>$data['manuf_seo_title']];
    $text['seo']['manuf_seo_desc'] = ['name'=>'Мета-тег Description производителя', 'row'=>5, 'text'=>$data['manuf_seo_desc']];
    $text['seo']['manuf_seo_keyw'] = ['name'=>'Мета-тег Keywords производителя', 'row'=>5, 'text'=>$data['manuf_seo_keyw']];

		//Фото отдельный подход
		if(!empty($data['cat'])){

			foreach($data['cat'] as $cat){
				$text['pr']['cat']['text'] .= $cat.PHP_EOL;
			}
		}

		//Фото отдельный подход
		if(!empty($data['img_info'])){

			foreach($data['img_info'] as $img){
				$text['pr']['img']['text'] .= $img.PHP_EOL;
			}
		}

		//Атрибуты отдельный подход
		if(!empty($data['attr'])){

			foreach($data['attr'] as $attr){
				
				if(!empty($attr[0])){
					@$text['pr']['attr']['text'] .= $attr[0].' => '.$attr[1].PHP_EOL; #Заглушил временно, можно удалить заглушку 
				}
			}
		}

		//Подготовка опций к дебагу.
		$opts_debu = [];
		$opts_debu['text'] = '';
		#{|}0
		if(!empty($data['opt_name'])){
			$opts_debu['name'] = explode('{next}', $data['opt_name']);
			$opts_debu['opt_value'] = explode('{next}', str_replace('{!na!}', '{csvnc}', $data['opt_value']));
			$opts_debu['opt_price'] = explode('{next}', str_replace('{!na!}', '{csvnc}', $data['opt_price']));
			$opts_debu['opt_quant'] = explode('{next}', str_replace('{!na!}', '{csvnc}', $data['opt_quant']));
			$opts_debu['opt_quant_d'] = explode('{next}', str_replace('{!na!}', '{csvnc}', $data['opt_quant_d']));
			$opts_debu['opt_data'] = explode('{next}', str_replace('{!na!}', '{csvnc}', $data['opt_data']));
			$deb_quant_d = $opts_debu['opt_quant_d'][0];
			foreach ($opts_debu['name'] as $key => $name) {
				if ($name != '{|}0'){
					$name = explode('{|}', $name);
					$dop = explode('}{', $opts_debu['opt_data'][$key]);
					if($dop[0] == '{required_1'){ $dop[0] = 'Да'; } else { $dop[0] = 'Нет'; }
					$dop[1] = str_replace('price_prefix_', '', $dop[1]);
					$dop[1] = str_replace('}', '', $dop[1]);

					//определяем колво.
					//Если для этой опции не указали значение по умолчанию.
					if(!isset($opts_debu['opt_quant_d'][$key])) { $opts_debu['opt_quant_d'][$key] = $deb_quant_d;}
					//Если нету колва то берем значение по умолчанию. 
					if (empty($opts_debu['opt_quant'][$key])) { $opts_debu['opt_quant'][$key] = '[Сработало по умолчанию, Кол-во = '.$opts_debu['opt_quant_d'][$key].']';}

					$opts_debu['text'] = $opts_debu['text'].
																'Название опции  => '.$name[0].PHP_EOL.
																'Значение опции  => '.$opts_debu['opt_value'][$key].PHP_EOL.
																'Цены опции      => '.$opts_debu['opt_price'][$key].PHP_EOL.
																'Кол-во опции    => '.$opts_debu['opt_quant'][$key].PHP_EOL.
																'ID по умолчанию => '.$name[1].PHP_EOL.
																'Обязательная    => '.$dop[0].PHP_EOL.
																'Префикс цены    => '.$dop[1].PHP_EOL.PHP_EOL.
																'##################################################'.PHP_EOL.PHP_EOL;
				}

			}

		}
		$text['pr']['opts'] = ['name'=>'Опции', 'row'=>19, 'text'=>$opts_debu['text']];

		//Создаем текст из раздела данные.
		$pr_data = '
		 [upc] => '.$data['upc'].PHP_EOL.
    '[ean] => '.$data['ean'].PHP_EOL.
    '[jan] => '.$data['jan'].PHP_EOL.
    '[isbn] => '.$data['isbn'].PHP_EOL.
    '[mpn] => '.$data['mpn'].PHP_EOL.
    'Расположение [location] => '.$data['location'].PHP_EOL.
    'Минимальное кол-во [minimum] => '.$data['minimum'].PHP_EOL.
    'Вычитать со склада [subtract] => '.$data['subtract'].PHP_EOL.
    'Размеры (Длина) [length] => '.$data['length'].PHP_EOL.
    'Размеры (Ширина) [width] => '.$data['width'].PHP_EOL.
    'Размеры (Высота) [height] => '.$data['height'].PHP_EOL.
    'Единица длины [length_class_id] => '.$data['length_class_id'].PHP_EOL.
    'Вес товара [weight] => '.$data['weight'].PHP_EOL.
    'Единица веса [weight_class_id] => '.$data['weight_class_id'].PHP_EOL.
    'Статус товара [status] => '.$data['status'].PHP_EOL.
    'Порядок сортировки [sort_order] => '.$data['sort_order'].PHP_EOL;

		$text['pr']['pr_data'] = ['name'=>'Дополнительные данные', 'row'=>16, 'text'=>trim($pr_data)];

		//создаем отладочную информацию для допусков страницы
		$pr_permit = '';
		if(!empty($data['permit_grans_text'])){

			foreach($data['permit_grans'] as $key => $permit){

				if($key <= 3 && empty($permit['permit'])){
					$pr_permit = $permit['log'].PHP_EOL;
				}

			}

		}
		$text['pr']['pr_permit_html'] = ['name'=>'Допуск страницы к Парсингу', 'row'=>5, 'text'=>$pr_permit];
    
	}
	#$this->wtfarrey($data);
	return $text;
}
############################################################################################
############################################################################################
#						Фунции отвечающие за просмотр страницы Менеджер ссылок
############################################################################################
############################################################################################

//обшая фунция по получению ссылок для парсинга.
public function getUrlToPars($dn_id, $list="", $http_code=""){

	//возврашаемый массив.
	#$data = ['links' => [], 'queue' => [], 'total' => []];

	//определяем о каком списке товаров идет речь.
	$where = " WHERE dn_id=".(int)$dn_id;
	//проверяем выбор списка ссылок.
	if($list != 0){
		$where .= " AND list =".(int)$list;
	}
	//проверяем выбранный список по ошибках
	if(!empty($http_code)){
		$where .= " AND error =".(int)$http_code;
	}

	#$this->wtfarrey($where);

	//получаем ссылки для обработки.
	$links = $this->db->query("SELECT link FROM ". DB_PREFIX ."pars_link".$where." AND scan=1 ORDER BY id ASC LIMIT 0,5");
	$links = $links->rows;

	//получаем все не обработанные ссылки.
	$queue = $this->db->query("SELECT COUNT(*) as count FROM ". DB_PREFIX ."pars_link".$where." AND scan=1");
  $queue = $queue->row['count'];

  //получаем все ссылки которые есть 
  $total = $this->db->query("SELECT COUNT(*) as count FROM ". DB_PREFIX ."pars_link".$where);
  $total = $total->row['count'];

  //получаем полный список ссылок.
  $full = $this->db->query("SELECT COUNT(*) as count FROM ". DB_PREFIX ."pars_link WHERE dn_id=".(int)$dn_id);
  $full = $full->row['count'];

  //формируем массив ответа.
  $data = ['links' => $links, 'queue' => $queue, 'total' => $total, 'full' => $full];
  #$this->wtfarrey($data);

  return $data;
}

//получаем список ссылок
public function getAllLinkList($dn_id){
	//получаем перечень списков
	$lists = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_link_list WHERE `dn_id`=".(int)$dn_id);
	$list_names = $lists->rows;
	return $list_names;
}

//получаем список ссылок
public function getAllLinkError($dn_id){
	//получаем списко ошибок.
	$list_errors = [];
	$errors = $this->db->query("SELECT DISTINCT error FROM ".DB_PREFIX."pars_link WHERE `dn_id`=".(int)$dn_id);
	$data['list_errors'] = [];
	foreach($errors->rows as $value){
		if($value['error'] != 0){ $list_errors[] = $value['error']; }
	}

	$list_errors = array_diff($list_errors, array(''));
	#$this->wtfarrey($list_errors);
	return $list_errors;
}

//сохраняем выбор списка для парсинга
public function saveLinkListAndError($data, $dn_id){
	#$this->wtfarrey($data);
	$this->db->query("UPDATE ".DB_PREFIX."pars_setting SET 
		link_list ='".$this->db->escape($data['link_list'])."', 
		link_error ='".$this->db->escape($data['link_error'])."' WHERE dn_id =".(int)$dn_id);
}

//рестарт ссылок на страницах парсинга.
public function restLinkToPars($data, $dn_id){
	#$this->wtfarrey($data);
	//определяем о каком списке товаров идет речь.
	$where = " WHERE dn_id=".(int)$dn_id;
	//проверяем выбор списка ссылок.
	if($data['link_list'] != 0){
		$where .= " AND list =".(int)$data['link_list'];
	}
	//проверяем выбранный список по ошибках
	if(!empty($data['link_error'])){
		$where .= " AND error =".(int)$data['link_error'];
	}

	//выполняем запрос на рестарт ссылок.
	$this->db->query("UPDATE `".DB_PREFIX."pars_link` SET `scan`=1".$where);
}

//Добавление нового списка
public function addNewLinkList($data, $dn_id){
	
	//проверяем есть ли такой список
	$check = $this->db->query("SELECT id FROM ".DB_PREFIX."pars_link_list WHERE dn_id=".$dn_id." AND name='".$this->db->escape($data['list_name_new'])."'");
	if($check->num_rows){
		$this->session->data['error'] = " Список с таким названием уже существует. Выберите другое название для списка.";
	}else{
		$this->db->query("INSERT INTO ".DB_PREFIX."pars_link_list SET dn_id=".$dn_id.", name='".$this->db->escape($data['list_name_new'])."'");
		$this->session->data['success'] = " Новый список успешно создан.";
	}
}	

//удаление списка
public function delLinkList($data, $dn_id){
	$this->db->query("UPDATE ".DB_PREFIX."pars_link SET list=0 WHERE list=".(int)$data['list_del']);
	$this->db->query("DELETE FROM `".DB_PREFIX."pars_link_list` WHERE id=".(int)$data['list_del']);
	$this->session->data['success'] = " Список успешно удален. Если в списке были ссылки они перенесены в общий список.";
}

//Фунция фильтрации ссылок.
public function urlsGetId($data, $dn_id){
	#$this->wtfarrey($data);
	//Составляем хвостик WHERE 
	$where = ' WHERE dn_id ='.(int)$dn_id;

	//если пришел массив с id списков
	if(!empty($data['list_name'])){
		$where .= " AND list in (".implode(',', $data['list_name']).")";
	}

	//если пришел запрос на фильтрацию по типу ошибки.
	if(!empty($data['list_error'])){
		$where .= " AND error in (".implode(',', $data['list_error']).")";
	}

	//пришел запрос на фильтрацию ссылок по состаюнию париснга пользователем
	if($data['link_scan'] != 'all'){
		$where .= " AND scan = ".(int)$data['link_scan'];
	}

	//пришел запрос на фильтрацию ссылок по состаюнию париснга кроном
	if($data['link_scan_cron'] != 'all'){
		$where .= " AND scan_cron = ".(int)$data['link_scan_cron'];
	}

	if(!empty($data['filters'])){
		//перебераем все фильтры
		foreach ($data['filters'] as $filter) {

			//для начала проверяем есть ли значение в этой фильтре
			if(empty($filter['value']) && $filter['value'] != '0'){
				$filter['value'] = '';
			}
				
			//определяем поле в таблице.
			if($filter['take_filtr'] == '0'){
				continue;
			}elseif($filter['take_filtr'] == 'string' || $filter['take_filtr'] == 'url_id'){ 

				//Если фильтра в базе данных делается то все в этом блоке. Выбираем метод фильтрации.
				if($filter['take_filtr'] == 'string'){
					$table = DB_PREFIX.'pars_link.link';
				}else{
					$table = DB_PREFIX.'pars_link.id';
				}

				#$this->wtfarrey($filter);
				$pos = $this->toolFilterPosition($filter['position'], $filter['style']);

				//делим значение на массив если ли это многомерное значение. Заодно удаляем пустые массивы
				$filter['value'] = explode('|', $filter['value']);
				foreach($filter['value'] as $key_value => $value){

					$value = $this->db->escape($value);
					if($key_value == 0){ 
						$where .= ' AND ('.$table.str_replace('{data}', $value, $pos); 
					}else{
						$where .= ' OR '.$table.str_replace('{data}', $value, $pos);
					}
				}

				$where .= ')';

			}elseif($filter['take_filtr'] == 'date_cach'){

				$table = DB_PREFIX.'pars_link.key_md5';

				$cache_dir = DIR_APPLICATION.'simplepars/cache_page/'.$dn_id.'/*.txt';
				$files = glob($cache_dir);
				$url_cached_date = [];
				$and = ' AND (';
				$bracket = '';
				foreach($files as $file){
					
					$date = date("Y-m-d H:i:s", filectime($file));
					$file = preg_replace('#(.*?)/cache_page/(.*?)/#', '', $file);
					$file = str_replace('.txt', '', $file);

					//если совпадает введенная дата, и дата кеширования.
					if( empty($filter['style']) && stripos($date, $filter['value']) !== false){
						$where .= $and.$table."='".$file."'";
						$and = ' OR ';
						$bracket = ')';
					}elseif( !empty($filter['style']) && stripos($date, $filter['value']) === false){
						$where .= $and.$table."='".$file."'";
						$and = ' OR ';
						$bracket = ')';
					}

				}

				$where .= $bracket;# Добавляется скобочка если было совпадение, маразм но что поделаеш :( 
			}
		}
	}

	#$this->wtfarrey($where);

	$urls_id = $this->db->query("SELECT id FROM ".DB_PREFIX."pars_link".$where);
	$urls_id = $urls_id->rows;
	#$urls_id = array_unique($urls_id, SORT_REGULAR);

	$list_id = '';
	foreach($urls_id as $key => $url){

		if($key){ $list_id .= ','.$url['id']; } else { $list_id = $url['id'];}

	}

	if(empty($list_id)){ $list_id = 0;}
	#$this->wtfarrey($list_id);
	return $list_id;
}

//Фильтрация ссылок
public function urlFilterToPage($data, $dn_id){
	//возврашаемый массив
	$answ = [];

	//определяем колво товаров на страницу
	$limit_start = ($data['page'] * $data['page_count']) - $data['page_count'];
	$limit_stop = $limit_start + $data['page_count'];
	$limit = ' LIMIT '.$limit_start.','.$limit_stop;

	//получаем список id товаров которые попадают под фильтры
	$urls_id = $this->urlsGetId($data, $dn_id);

	//получаем ссылки из базы
	$urls = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_link WHERE id in (".$urls_id.") ORDER BY id".$limit);
	$urls = $urls->rows;

	//Получаем списко списков ссылок
	$link_list = $this->db->query("SELECT id, name FROM ".DB_PREFIX."pars_link_list WHERE dn_id = ".$dn_id);
	$link_list = $link_list->rows;
	$link_list = array_column($link_list, 'name', 'id');
	
	#Проверяем есть ли такой файл кеша.
	foreach($urls as &$value){
		
		//присваеваем название списков ссылкам.			
		if(!empty($link_list[$value['list']])){
			$value['list'] = $link_list[$value['list']];
		}else{
			$value['list'] = 'Общий';
		}

		//работа с ошибками.
		if(empty($value['error'])){
			$value['error'] = '';
		}elseif($value['error'] > 0){
			$value['error'] = '<b class="text-danger">'.$value['error'].'</b>';
		}

		//Информация по кешу.
		$file = DIR_APPLICATION.'simplepars/cache_page/'.$dn_id.'/'.$value['key_md5'].'.txt';
		$value['cached_time'] = 'not cached';
		if(file_exists($file)){
			
			$info = stat($file);
			$value['cached_time'] = date("Y-m-d H:i:s", $info['mtime']);
			$value['size'] = $info['size'];
			#$this->wtfarrey($value);
		}
	}
	//Передаем данные на ответ.
	$answ['urls'] = $urls;
	$total_urls = $this->db->query("SELECT COUNT(*) as count FROM ".DB_PREFIX."pars_link WHERE id in (".$urls_id.")");
	$answ['total'] = $total_urls->row['count'];

	#$this->wtfarrey($data);
	#$this->wtfarrey($urls);

	return $answ;
}

//контроллер действий.
public function urlControlerFunction($data, $dn_id){

	#$this->wtfarrey($data);
	//проверяем выбрано ли действие.
	if(!empty($data['do_action'])){
		//Получаем where
		$urls_id = $this->urlsGetId($data, $dn_id);

		//определяем какая фунция была выбрана.
		if($data['do_action'] == 'url_change_list'){

			$this->urlActionChangeList($data, $urls_id, $dn_id);

		}elseif($data['do_action'] == 'url_del_error'){

			$this->urlActionDelErrors($data, $urls_id, $dn_id);

		}elseif($data['do_action'] == 'url_del_cache'){

			$this->urlActionDelCache($data, $urls_id, $dn_id);

		}elseif($data['do_action'] == 'url_replace'){

			$this->urlActionFindReplace($data, $urls_id, $dn_id);

		}elseif($data['do_action'] == 'url_del'){

			$this->urlActionDeletUrls($data, $urls_id, $dn_id);

		}
		
		#$this->wtfarrey($urls_id);
		exit(json_encode("Действие выполнено!"));
	}
	
}

//Фунци изменения списка в ссылке
public function urlActionChangeList($data, $urls_id, $dn_id){

	$this->db->query("UPDATE ".DB_PREFIX."pars_link SET list = ".(int)$data['new_list']." WHERE id in (".$urls_id.")");
}

//Фунция удаления ошибок с ссылок.
public function urlActionDelErrors($data, $urls_id, $dn_id){

	//проверяем какие ошибки сбрасывать.
	if($data['del_errors'] == 'all'){
		$where_error = '';	
	}else{
		$where_error = " AND error =".$this->db->escape($data['del_errors']);
	}
	//Выполняем запрос.
	$this->db->query("UPDATE ".DB_PREFIX."pars_link SET error = '' WHERE id in (".$urls_id.")".$where_error);
}

//Фунция очистки кеша.
public function urlActionDelCache($data, $urls_id, $dn_id){
	
	//проверяем какой кеш чистить.
	if($data['which_cache'] == 'url_get'){
		//Получаем спсико ссылок кеш которых нужно почистить.
		$urls = $this->db->query("SELECT key_md5 FROM ".DB_PREFIX."pars_link WHERE id in (".$urls_id.")");
		$urls = $urls->rows;
		
		//если массив не пустой приступаем к очистки кеша. 
		if(!empty($urls)){

			//перебираем масив и удаляем кеш.
			foreach($urls as $url){
				$path = DIR_APPLICATION.'simplepars/cache_page/'.$dn_id.'/'.$url['key_md5'].'.txt';
				@unlink($path);
			}			
		}
	}elseif($data['which_cache'] == 'url_all'){
		$this->urlDelAllCache($dn_id);
	}
}

//фунция поиск замена в ссылках.
public function urlActionFindReplace($data, $urls_id, $dn_id){

	//получаем список ссылок с которыми будем работать.
	$urls = $this->db->query("SELECT id, link FROM ".DB_PREFIX."pars_link WHERE id in (".$urls_id.")");
	$urls = $urls->rows;

	//если ссылки есть по нужным фильтрам тогда приступаем к работе.
	if(!empty($urls)){

		//преобразуем правила. 
		$data['rules'] = $this->parseRulesToReplace($data);

		//перебираем ссылки.
		foreach($urls as $url){
			//Для сокрашение запросов в базу данных
			$standard = $url['link'];

			//если есть правила для поиск замеены
			if(!empty($data['rules'])){
				//перебираем правила поиск замены
				foreach($data['rules'] as $rule){

	  			if(isset($rule[0]) && isset($rule[1])){

	  				$rule[0] = $this->pregRegLeft($rule[0]);
	  				$rule[1] = $this->pregRegRight($rule[1]);
	  				$url['link'] = preg_replace($rule[0], $rule[1], html_entity_decode($url['link']));
	  			}
	  		}
  		}
  		//если есть что добавить вконец добавляем
  		if(isset($data['url_end'])){
  			$url['link'] = $url['link'].$data['url_end'];
  		}

  		//проверяем в ссылке что то изменилось.
  		if($standard != $url['link']){
	  		//проверяем что делать обновить текушие ссылки. Или добавить новые.
	  		//0 - обновить | 1 - Добавить как новую.
	  		if(empty($data['what_do'])){
	  			
	  			//очишаем
	  			$url['link'] = $this->ClearLink($url['link']);
	  			//Зашита от дурака, если кто то догадается сделать поискз заменга протакола.
	  			if(preg_match('#(^http://)|(^https://)#', $url['link'])){
		  			//обновляем ссылку
		  			$this->db->query("UPDATE IGNORE ".DB_PREFIX."pars_link SET 
		  				link ='".$this->db->escape($url['link'])."',
		  				key_md5 = '".md5($dn_id.$url['link'])."' 
		  				WHERE id =".$url['id']);
	  			}
	  		}else{
	  			//Зашита от дурака, если кто то догадается сделать поискз заменга протакола.
	  			if(preg_match('#(^http://)|(^https://)#', $url['link'])){
	  				$this->AddParsLink($url['link'], $dn_id);
	  			}
	  			
	  		}
  		}
		}
	}
}

//Фунция по удалению ссылок
public function urlActionDeletUrls($data, $urls_id, $dn_id){
	#$this->wtfarrey($urls_id);
	//получаем списко файлов которые нужно удалить.
	$files = $this->db->query("SELECT key_md5 FROM `".DB_PREFIX."pars_link` WHERE id in (".$urls_id.")");
	#$this->wtfarrey($files);
	$cache_dir = DIR_APPLICATION.'simplepars/cache_page/'.$dn_id.'/';
	foreach($files->rows as $file){
		@unlink($cache_dir.$file['key_md5'].'.txt');
	}
	
	$this->db->query("DELETE FROM `".DB_PREFIX."pars_link` WHERE id in (".$urls_id.")");
}

//Вспомагательная фунция удаления файлов кеша.
public function urlDelAllCache($dn_id){
	//Адерсс директории с фалами.
	$cache_dir = DIR_APPLICATION.'simplepars/cache_page/'.$dn_id.'/*.txt';
	$files = glob($cache_dir);
	//производим удаление
	array_map('unlink', $files);
}

//Сохраняем настройки
public function saveCacheForm($data, $dn_id){
	#$this->wtfarrey($data);
	$this->db->query("UPDATE ". DB_PREFIX ."pars_setting  SET 
	  	pars_pause='".$this->db->escape($data['pars_pause'])."', 
	  	thread='".$this->db->escape($data['thread'])."' WHERE dn_id=".(int)$dn_id);
}

//Контролдлер парсинга в кеш
public function controlParsToCache($dn_id){
  $setting = $this->getSetting($dn_id);

  //Получаем списк неспарсенных ссылок.
	$pars_url = $this->getUrlToPars($dn_id, $setting['link_list'], $setting['link_error']);

  #Если ссылок нету завершаем работу модуля.
  if(empty($pars_url['links'])){

    $answ['progress'] = 100;
    $answ['clink'] = ['link_scan_count' => $pars_url['total'], 'link_count' => $pars_url['queue'],];
    $this->answjs('finish','Парсинг закончился, ссылок больше нет﻿',$answ);

  }else{

  	//собираем массив ссылок для мульти запроса.
  	$urls = [];
  	foreach($pars_url['links'] as $key => $url){
  		if($key < $setting['thread']) {$urls[] = $url['link']; } else { break; }
  	}

  	$browser = $this->getBrowserToCurl($dn_id);
  	$browser['cache_page'] = 2;
  	$datas = $this->multiCurl($urls, $dn_id, $browser);

  	//Далее разбираем данные из мульти курла и делаем все нужные записи.
  	foreach($datas as $link => $data){
  		
  		//производим зяпись лога курл, и паролельно проверяем нужно ли делать дальнейшую работу.
  		$curl_error = $this->sentLogMultiCurl($data ,$dn_id);
  
			#помечаем ссылку как отсканированная
    	$this->db->query("UPDATE ". DB_PREFIX ."pars_link SET scan=0, error='".$curl_error['http_code']."' WHERE link='".$data['url']."' AND dn_id=".$dn_id);

  		//если пришла ошибка заканчиваем эту итерацию и переходим к следующей.
  		if($curl_error['error']){ 
  			continue;
  		}
			//Здесь должно что то делатся.
			//Но это кеш по этому ничего ;-)  		
  	}
    
    #считаем процент для прогрес бара
    $scan = ($pars_url['total'] - $pars_url['queue']);
    $progress = $scan/($pars_url['total']/100);
    $answ['progress'] = $progress;
    $answ['clink'] = [
                       'link_scan_count' => $scan,
                       'link_count' => $pars_url['queue'],
                      ];
    #пауза парсинга
    $this->timeSleep($setting['pars_pause']);
    $this->answjs('go','Производится парсинг',$answ);
    #exit(json_encode($answ));
  }
}

############################################################################################
############################################################################################
#						TOOLS
############################################################################################
############################################################################################

//сохранение шаблона редактора товара
public function toolAddPattern($data, $dn_id){
	$pattern_name = $data['pattern_name'];
	$data = json_encode($data);
	//записываем настройки в базу данных.
	$this->db->query("INSERT INTO ".DB_PREFIX."pars_tools_pattern 
										SET dn_id =".(int)$dn_id.", 
										name = '".$this->db->escape($pattern_name)."',
										setting = '".$this->db->escape($data)."'");
}

//обновление шаьлона
public function toolUpdatePattern($data, $dn_id){
	#$this->wtfarrey($data);
	$pattern_name = $data['pattern_name'];
	$pattern_id = (int)$data['pattern_take'];
	$data = json_encode($data);
	//записываем настройки в базу данных.
	$this->db->query("UPDATE ".DB_PREFIX."pars_tools_pattern 
										SET dn_id =".(int)$dn_id.", 
										name = '".$this->db->escape($pattern_name)."',
										setting = '".$this->db->escape($data)."' WHERE id =".$pattern_id);
}

//удаление шаблона
public function toolDelPattern($pt_id){
	#$this->wtfarrey($pt_id);
	#Запрос на удаление
	$this->db->query("DELETE FROM `".DB_PREFIX."pars_tools_pattern` WHERE id = ".(int)$pt_id);
}

//получить список всех шаблонов, данного проекта.
public function toolGetAllPatterns($dn_id){
	$patterns = [];
	//получаем все шаблоны
	$data = $this->db->query("SELECT id, name FROM ".DB_PREFIX."pars_tools_pattern WHERE dn_id =".(int)$dn_id);
	if($data->num_rows != 0){

		$patterns = $data->rows;

	}
	#$this->wtfarrey($patterns);
	return $patterns;
}

//Получение данных для страницы.
public function toolGetPatternToPage($pt_id){
	$data = [];

	//получаем данные о патерне
	$temp = $this->toolGetPattern($pt_id);
	
	if(!empty($temp['setting'])){
		$data = $temp['setting'];
	}

	//получаем id последнего ключа для twig
	if(!empty($data['filters'])){
	  if (!is_array($data['filters']) || empty($data['filters'])) {
	    $data['key_f_last'] = NULL;
	  }else{
	    $data['key_f_last'] = array_keys($data['filters'])[count($data['filters'])-1];
	  }
	}else{
		$data['key_f_last'] = 0;
	}

	if(empty($data['cats'])){ $data['cats'] = [];}
	#$this->wtfarrey($pattern);
	#$this->wtfarrey($data);
	return $data;
}

//Получение данных о патерне.
public function toolGetPattern($pt_id){
	$data = [];

	if(!empty($pt_id)){
		$pattern = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_tools_pattern WHERE id = ".(int)$pt_id);
		$pattern = $pattern->row;

		//проверяем что бы настройки были не пустые.
		if(!empty($pattern['setting'])){
			$pattern['setting'] = htmlspecialchars_decode($pattern['setting']);
			$pattern['setting'] = json_decode($pattern['setting'], true);
			#$this->wtfarrey($pattern);
		}
	}

	if(empty($pattern['setting']['cats'])){ $pattern['setting']['cats'] = [];}
	$data = $pattern;

	#$this->wtfarrey($data);
	return $data;
}

//Преобразования фильтров, для sql запроса
public function toolFilterPosition($pos, $style){

	//перобразуем сушности html в теги
	$pos = html_entity_decode($pos);
	
	if($pos == '={data}' && $style == 1){
	
		$pos = " != '{data}'";
	
	}elseif($pos == '={data}' && $style == 0){

		$pos = " = '{data}'";

	}elseif($pos == '%{data}' && $style == 1){

		$pos = " NOT LIKE '%{data}'";

	}elseif($pos == '%{data}' && $style == 0){

		$pos = " LIKE '%{data}'";

	}elseif($pos == '{data}%' && $style == 1){

		$pos = " NOT LIKE '{data}%'";

	}elseif($pos == '{data}%' && $style == 0){

		$pos = " LIKE '{data}%'";

	}elseif($pos == '%{data}%' && $style == 1){

		$pos = " NOT LIKE '%{data}%'";

	}elseif($pos == '%{data}%' && $style == 0){

		$pos = " LIKE '%{data}%'";

	}elseif($pos == '>={data}' && $style == 1){

		$pos = " <= '{data}'";

	}elseif($pos == '>={data}' && $style == 0){

		$pos = " >= '{data}'";

	}elseif($pos == '<={data}' && $style == 1){

		$pos = " >= '{data}'";

	}elseif($pos == '<={data}' && $style == 0){

		$pos = " <= '{data}'";

	}

	#$this->wtfarrey($pos);
	return $pos;
}

// Костыльная переделка пагинации под мои нужды
public function toolRenderPage($html){
	$html = str_replace('class="pagination"', "class='pagination' id='del_ul'", $html);

	//позорише мое, если читаете эту фунцию простите меня за такой подход.
	$html = preg_replace('#<a href="(.*?)">(|\&lt;)</a>#', '<button type="button" class="btn btn btn-sm" onclick=\'controlFilter(1)\'>|&lt;</button> ', $html);
	$html = preg_replace('#<a href="(.*?)">1</a>#', '<button type="button" class="btn btn btn-sm" onclick=\'controlFilter(1)\'>1</button> ', $html);
	#<a href="(.*?)">|&lt;</a>
	
	$html = preg_replace('#<a href="(.*?)page=#', '<button type="button" class="btn btn btn-sm" onclick="controlFilter(', $html);
	$html = str_replace('">', ')">', $html);
	$html = str_replace('</a>', '</button> ', $html);
	$html = str_replace('class="active"', '', $html);
	
	$html = str_replace('<span>', '<button type="button" name="page" class="btn btn-primary btn-sm">', $html);
	$html = str_replace('</span>', '</button> ', $html);

	#$this->wtfarrey($html);

	return $html;
}

//Ресайз фото для вывода в таблице.
public function toolResizeImg($img){
	$image = $img;
	if (is_file(DIR_IMAGE . $img)) {
		$image = $this->model_tool_image->resize($img, 40, 40);
	} else {
		$image = $this->model_tool_image->resize('no_image.png', 40, 40);
	}
	#$this->wtfarrey($image);
	return $image;
}

//получения списка категорий для страницы тулс
public function toolMadeCategoryToPage(){
	$data = $this->madeCatTree(1);
	$categorys = [];
	if(!empty($data)){
		foreach($data as $key => $value){
			$categorys[] = ['id' =>$key, 'name'=>$value];
		}
	}
	
	#$this->wtfarrey($categorys);
	return $categorys;
}

//Контроллер выполнения фунций над товарами
public function toolControlerFunction($data, $dn_id, $who = 'user'){

	//Проверяем какой язык выбран, если никакой берем язык по умолчанию.
	if(empty($data['langs'])){ $data['langs'][] = $this->getLangDef(); }
	#$this->wtfarrey($data);
	//проверяем выбрано ли действие.
	if(!empty($data['do_tools'])){
		//Получаем where
		$prs_id = $this->toolGetPrsId($data);

		//определяем какая фунция была выбрана.
		if($data['do_tools'] == 'change_price'){

			$this->toolChangePrice($data, $prs_id, $dn_id);

		}elseif($data['do_tools'] == 'change_quant'){

			$this->toolChangeQuant($data, $prs_id, $dn_id);

		}elseif($data['do_tools'] == 'change_status'){

			$this->toolChangeStatus($data, $prs_id, $dn_id);
			
		}elseif($data['do_tools'] == 'change_stock_status'){

			$this->toolChangeStockStatus($data, $prs_id, $dn_id);
			
		}elseif($data['do_tools'] == 'change_cats_add'){

			$this->toolAddCatsToProducts($data, $prs_id, $dn_id);
			
		}elseif($data['do_tools'] == 'change_cats_ch'){

			$this->toolChangeCatsToProducts($data, $prs_id, $dn_id);
			
		}elseif($data['do_tools'] == 'change_main_cat'){

			$this->toolChangeMainCatToProducts($data, $prs_id, $dn_id);
			
		}elseif($data['do_tools'] == 'change_manuf'){

			$this->toolChangeManuf($data, $prs_id, $dn_id);
			
		}elseif($data['do_tools'] == 'change_dn'){

			$this->toolChangeDn($data, $prs_id, $dn_id);
			
		}elseif($data['do_tools'] == 'change_meta'){
			
			$this->toolChangeMeta($data, $prs_id, $dn_id);
		
		}elseif($data['do_tools'] == 'change_url'){
			
			$this->toolChangeUrl($data, $prs_id, $dn_id);

		}elseif($data['do_tools'] == 'replace'){

			$this->toolFindReplace($data, $prs_id, $dn_id);

		}elseif($data['do_tools'] == 'del_product'){
			
			$this->toolDelProducts($data, $prs_id, $dn_id);

		}elseif($data['do_tools'] == 'del_product_img'){
			
			$this->toolDelProductsImg($data, $prs_id, $dn_id);

		}elseif($data['do_tools'] == 'del_price_spec'){
			
			$this->toolDelPriceSpec($data, $prs_id, $dn_id);
			
		}elseif($data['do_tools'] == 'del_attr_product'){
			
			$this->toolDelAttrProducts($data, $prs_id, $dn_id);

		}elseif($data['do_tools'] == 'del_opt_product'){
			
			$this->toolDelOptProducts($data, $prs_id, $dn_id);

		}

		//определяем кто вызвал действие. Если пользователь то даем ответ. А если крон молчим.
		if($who == 'user'){ exit(json_encode("Действие выполнено!")); }
		
		#$this->wtfarrey($where);
	}
	
	#$this->wtfarrey($data);
}

//Изменить цену
public function toolChangePrice($data, $prs_id, $dn_id){

	//приводим значение числа.
	$data['value'] = (float)trim(str_replace(',', '.', $data['value']));
	$set = '';
	if($data['operator'] == '='){
		$sql = "UPDATE `".DB_PREFIX."product` SET price = ".$data['value']." WHERE product_id IN (".$prs_id.")";
	}else{
		$sql = "UPDATE `".DB_PREFIX."product` SET price = price ".$data['operator'].$data['value']." WHERE product_id IN (".$prs_id.")";
	}

	#$this->wtfarrey($sql);
	$this->db->query($sql);
}

//Изменить колво
public function toolChangeQuant($data, $prs_id, $dn_id){
	//приводим значение числа.
	$data['value'] = (int)trim(str_replace(',', '.', $data['value']));
	$set = '';
	if($data['operator'] == '='){
		$sql = "UPDATE `".DB_PREFIX."product` SET quantity = ".$data['value']." WHERE product_id IN (".$prs_id.")";
	}else{
		$sql = "UPDATE `".DB_PREFIX."product` SET quantity = quantity ".$data['operator'].$data['value']." WHERE product_id IN (".$prs_id.")";
	}

	
	#$this->wtfarrey($sql);
	$this->db->query($sql);
}

//Изменить статус в товарах
public function toolChangeStatus($data, $prs_id, $dn_id){
	//приводим значение числа.
	$data['value'] = (int)$data['value'];
	$sql = "UPDATE `".DB_PREFIX."product`	SET	status = ".$data['value']." WHERE product_id IN (".$prs_id.")";
	#$this->wtfarrey($sql);
	$this->db->query($sql);
}

//Изменение статуса товара при нулевом остатке
public function toolChangeStockStatus($data, $prs_id, $dn_id){
	$data['value'] = (int)$data['value'];
	//делаем действия только в том случаи если сток статус был выбран
	if(!empty($data['value'])){
		$sql = "UPDATE `".DB_PREFIX."product`	SET	stock_status_id = ".$data['value']." WHERE product_id IN (".$prs_id.")";
		#$this->wtfarrey($sql);
		$this->db->query($sql);
	}
}

//Добавляем товар в дополнительные категории.
public function toolAddCatsToProducts($data, $prs_id, $dn_id){

	//для начала проверяем что бы под фильтры папал хоть один товар
	if(!empty($prs_id)){
		
		//преобразуем строку с id товаров в массив.
		$pr_id_arr = explode(',', $prs_id);

		//проверяем что бы пользователь не забыл передать списко категорий.
		if(!empty($data['new_cats'])){

			//перебераем массив категорий
			foreach($data['new_cats'] as $cat){

				//переберираем массив товаров
				foreach($pr_id_arr as $product_id){
					$this->db->query("INSERT IGNORE INTO `".DB_PREFIX."product_to_category` SET product_id = ".$product_id.", category_id = ".$cat);
				}

			}

		}
		
	}	
}

//Заменяем все категории в товаре.
public function toolChangeCatsToProducts($data, $prs_id, $dn_id){

	//для начала проверяем что бы под фильтры папал хоть один товар
	if(!empty($prs_id)){
		
		//преобразуем строку с id товаров в массив.
		$pr_id_arr = explode(',', $prs_id);
		$setting = $this->getSetting($dn_id);

		// Удаляем все записи категорий в товаре.
		$this->db->query("DELETE FROM `".DB_PREFIX."product_to_category` WHERE `product_id` IN (".$prs_id.")");

		//проверяем какая версия движка стоит. И указываем главную категорию
		if( !empty($data['main_cat']) && ($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'ocstore3') ){

			//Удаляем из массива с новыми категориями категорию равную главной.
			$data['main_cat'] = (int)$data['main_cat'];
			unset($data['new_cats'][$data['main_cat']]);

			//записываем в товар новую главную категорию
			foreach($pr_id_arr as $product_id){
				$this->db->query("INSERT IGNORE INTO `".DB_PREFIX."product_to_category` SET product_id = ".$product_id.", category_id = ".$data['main_cat'].", main_category = 1");
			}

		}

		//проверяем что бы пользователь не забыл передать списко категорий.
		if(!empty($data['new_cats'])){

			//перебераем массив категорий
			foreach($data['new_cats'] as $cat){

				//переберираем массив товаров
				foreach($pr_id_arr as $product_id){
					$this->db->query("INSERT IGNORE INTO `".DB_PREFIX."product_to_category` SET product_id = ".$product_id.", category_id = ".$cat);
				}

			}

		}
	}
}

//Заменяем все главную категорию в товаре.
public function toolChangeMainCatToProducts($data, $prs_id, $dn_id){
	
	//для начала проверяем что бы под фильтры папал хоть один товар
	if(!empty($prs_id)){
		
		//преобразуем строку с id товаров в массив.
		$pr_id_arr = explode(',', $prs_id);
		$setting = $this->getSetting($dn_id);

		// Удаляем все записи категорий в товаре.
		$this->db->query("DELETE FROM `".DB_PREFIX."product_to_category` WHERE `product_id` IN (".$prs_id.") AND main_category = 1");

		//проверяем какая версия движка стоит. И указываем главную категорию
		if( !empty($data['main_cat']) && ($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'ocstore3') ){

			//Удаляем из массива с новыми категориями категорию равную главной.
			$data['main_cat'] = (int)$data['main_cat'];

			//записываем в товар новую главную категорию
			foreach($pr_id_arr as $product_id){
				$this->db->query("INSERT IGNORE INTO `".DB_PREFIX."product_to_category` SET product_id = ".$product_id.", category_id = ".$data['main_cat'].", main_category = 1");
			}

		}
	}
}

//изменить производителя в товаре. 
public function toolChangeManuf($data, $prs_id, $dn_id){
	$data['new_manuf'] = (int)$data['new_manuf'];

	//Выполняем обновление информации об производителе.
	if(!empty($data['new_manuf'])){
		$sql = "UPDATE `".DB_PREFIX."product`	SET	manufacturer_id = ".$data['new_manuf']." WHERE product_id IN (".$prs_id.")";
		#$this->wtfarrey($sql);
		$this->db->query($sql);
	}
}

//изменить проект в товаре
public function toolChangeDn($data, $prs_id, $dn_id){
	//приводим значение числа.
	$data['value'] = (int)$data['value'];
	$sql = "UPDATE `".DB_PREFIX."product` SET	dn_id = ".$data['value']." WHERE product_id IN (".$prs_id.")";
	#$this->wtfarrey($sql);
	$this->db->query($sql);
}

//изменить мета данные
public function toolChangeMeta($data, $prs_id, $dn_id){
	$setting = $this->getSetting($dn_id);

	//составляем запрос на рботу с языковыми файлами
	$langs_id = implode(',', $data['langs']);
	#$this->wtfarrey($langs_id);
	//определяем где заполнять.
	if($data['operator'] == 'product'){ 

		$seo = $this->db->query("SELECT seo_h1, seo_title, seo_desc, seo_keyw FROM `".DB_PREFIX."pars_prsetup` WHERE dn_id =".$dn_id);
		$seo = $seo->row;

		//вырезаем все границы потому что их тут не может быть.
		foreach($seo as &$value){
			$value = $this->db->escape(preg_replace('#\{gran_(.*?)\}#', '', $value));
		}

		//Проверяем какая версия движка 
		$ocstore = '';
		if($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'ocstore3'){
			$ocstore = ", meta_h1 ='".$seo['seo_h1']."'";
		}

		//составляем строку запроса.
		$sql = "UPDATE ".DB_PREFIX."product_description 
		SET meta_title = '".$seo['seo_title']."', 
		meta_description = '".$seo['seo_desc']."', 
		meta_keyword = '".$seo['seo_keyw']."'".$ocstore." WHERE product_id IN (".$prs_id.") AND language_id IN (".$langs_id.")";

		$this->db->query($sql);

	}elseif($data['operator'] == 'category'){

		//проверяем выбрали ли пользователь категории из списка, если да то берем напрямую их id
		if(!empty($data['cats'])){
			
			$ct_ids = implode(',', $data['cats']);
		
		} else {

			//получаем список товаров чьи категории будм править.
			$categorys = $this->db->query("SELECT category_id FROM ".DB_PREFIX."product_to_category WHERE product_id IN (".$prs_id.")");
			$categorys = $categorys->rows;
			$categorys = array_unique($categorys, SORT_REGULAR);
			//набор id для редактирования категорий.
			$ct_ids = '';
			foreach ($categorys as $key => $category) {
				if($key == 0){ $ct_ids = $category['category_id'] ;}else{ $ct_ids .= ','.$category['category_id']; }
			}
		
		}

		//если есть категории тогда приступаем обновлять
		if($ct_ids){
			$seo = $this->db->query("SELECT cat_seo_h1, cat_seo_title, cat_seo_desc, cat_seo_keyw FROM `".DB_PREFIX."pars_prsetup` WHERE dn_id =".$dn_id);
			$seo = $seo->row;
			
			//вырезаем все границы потому что их тут не может быть.
			foreach($seo as &$value){
				$value = $this->db->escape(preg_replace('#\{gran_(.*?)\}#', '', $value));
			}

			
			//Проверяем какая версия движка 
			$ocstore = '';
			if($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'ocstore3'){
				$ocstore = ", meta_h1 ='".$seo['cat_seo_h1']."'";
			}

			//обновляем сео данные категории
			$sql = "UPDATE ".DB_PREFIX."category_description SET 
			meta_title ='".$seo['cat_seo_title']."', 
			meta_description ='".$seo['cat_seo_desc']."', 
			meta_keyword ='".$seo['cat_seo_keyw']."'".$ocstore." WHERE category_id IN (".$ct_ids.") AND language_id IN (".$langs_id.")";

			
			$this->db->query($sql);
		}

	}elseif($data['operator'] == 'manuf'){

		if($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'ocstore3'){
			
			//проверяем выбрали ли пользователь Производителей из списка, если да то берем напрямую их id
			if(!empty($data['manufs'])){
				
				$mf_ids = implode(',', $data['manufs']);
			
			} else {

				$manufs = $this->db->query("SELECT manufacturer_id FROM ".DB_PREFIX."product WHERE product_id IN (".$prs_id.")");
				$manufs = $manufs->rows;
				$manufs = array_unique($manufs, SORT_REGULAR);

				//набор id для редактирования категорий.
				$mf_ids = '';
				foreach ($manufs as $key_mf => $manuf) {
					if($key_mf == 0){ $mf_ids = $manuf['manufacturer_id'] ;}else{ $mf_ids .= ','.$manuf['manufacturer_id']; }
				}
			}

			//Если есть id для редактирования тогда выполняем запрос.
			if($mf_ids){

				$seo = $this->db->query("SELECT manuf_seo_h1, manuf_seo_title, manuf_seo_desc, manuf_seo_keyw FROM `".DB_PREFIX."pars_prsetup` WHERE dn_id =".$dn_id);
				$seo = $seo->row;
				//вырезаем все границы потому что их тут не может быть.
				foreach($seo as &$value){
					$value = $this->db->escape(preg_replace('#\{gran_(.*?)\}#', '', $value));
				}
				//обновляем сео данные производителей
				$sql = "UPDATE ".DB_PREFIX."manufacturer_description SET 
				meta_h1 ='".$seo['manuf_seo_h1']."', 
				meta_title ='".$seo['manuf_seo_title']."', 
				meta_description ='".$seo['manuf_seo_desc']."', 
				meta_keyword ='".$seo['manuf_seo_keyw']."' WHERE manufacturer_id IN (".$mf_ids.") AND language_id IN (".$langs_id.")";

				$this->db->query($sql);
			}

		}

	}
}

//заполнить юрл
public function toolChangeUrl($data, $prs_id, $dn_id){

	//получаем глобальные настройки. И определяем какой движок используется.
	$setting = $this->getSetting($dn_id);

	//составляем запрос на рботу с языковыми файлами
	$langs_id = implode(',', $data['langs']);

	//Из за разности движков определяем в какой таблице нужно работать.
	$table = '';
	if($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'opencart2'){
		$table = DB_PREFIX."url_alias"; 
	}elseif($setting['vers_op'] == 'ocstore3' || $setting['vers_op'] == 'opencart3'){
		$table = DB_PREFIX."seo_url"; 
	}
	
	#$this->wtfarrey($table);

	if($data['operator'] == 'product'){ 

		//префикс запрорса.
		$prefix = "product_id=";
		//опередяем какие товары обновлять. 
		$products = $this->db->query("SELECT product_id, name, language_id FROM ".DB_PREFIX."product_description 
																WHERE	product_id IN (".$prs_id.") AND language_id IN (".$langs_id.")");
		$products = $products->rows;
		#$this->wtfarrey($products);
		foreach($products as $product){
			//преобразовываем имя в seo_url
			$product['seo_url'] = $this->db->escape(substr($this->madeUrl($product['name']), 0, 255));
			unset($product['name']);

			//Удаляем запись если такая была ранние
			$this->db->query("DELETE FROM ".$table." WHERE query ='".$prefix.$product['product_id']."'");

			//проверяем есть ли такой url в базе.
			$count_pr = $this->db->query("SELECT COUNT(*) as count FROM ".$table." WHERE keyword = '".$product['seo_url']."'");

			//если такой юрл есть то добавляем в конце id товара
			if($count_pr->row['count']){
				//получаем длину id товара. И отнимаем ее от 255 что бы добавить туда id товара
				$strlen = (255 - (strlen($product['product_id']) + 1));
				$product['seo_url'] = substr($product['seo_url'], 0, $strlen).'-'.$product['product_id'];
			}
			
			//выполняем запрос на добавление seo url товара
			if($setting['vers_op'] == 'ocstore3' || $setting['vers_op'] == 'opencart3'){
				$sql = "INSERT INTO ".$table." SET keyword = '".$product['seo_url']."', language_id=".$product['language_id'].", query = '".$prefix.$product['product_id']."'";
				$this->db->query($sql);
			}else{
				$sql = "INSERT INTO ".$table." SET keyword = '".$product['seo_url']."', query = '".$prefix.$product['product_id']."'";
				$this->db->query($sql);
			}
		}

	}elseif($data['operator'] == 'category'){

		//префикс запрорса.
		$prefix = "category_id=";

		//проверяем выбрали ли пользователь категории из списка, если да то берем напрямую их id
		if(!empty($data['cats'])){
			
			$ct_ids = implode(',', $data['cats']);
		
		} else {
			//опередяем какие Категории. 
			$categorys = $this->db->query("SELECT category_id FROM ".DB_PREFIX."product_to_category WHERE product_id IN (".$prs_id.")");
			$categorys = $categorys->rows;
			$categorys = array_unique($categorys, SORT_REGULAR);

			//набор id для редактирования категорий.
			$ct_ids = '';
			foreach ($categorys as $key => $category) {
				if($key == 0){ $ct_ids = $category['category_id'] ;}else{ $ct_ids .= ','.$category['category_id']; }
			}
		}

		//если есть категории который нужно править продолжаем.
		if($ct_ids){

			$categorys = $this->db->query("SELECT category_id, name, language_id FROM ".DB_PREFIX."category_description 
																		WHERE category_id IN (".$ct_ids.") AND language_id IN (".$langs_id.")");
			$categorys = $categorys->rows;

			foreach($categorys as $category){

				$category['seo_url'] = $this->db->escape(substr($this->madeUrl($category['name']), 0, 255));
				unset($category['name']);

				//Удаляем запись если такая была ранние
				$this->db->query("DELETE FROM ".$table." WHERE query ='".$prefix.$category['category_id']."'");

				//проверяем есть ли такой url в базе.
				$count_ct = $this->db->query("SELECT COUNT(*) as count FROM ".$table." WHERE keyword = '".$category['seo_url']."'");

				//если такой юрл есть то добавляем в конце id товара
				if($count_ct->row['count']){
					//получаем длину id товара. И отнимаем ее от 255 что бы добавить туда id товара
					$strlen = (255 - (strlen($category['category_id']) + 1));
					$category['seo_url'] = substr($category['seo_url'], 0, $strlen).'-'.$category['category_id'];
				}

				//выполняем запрос на добавление seo url категории
				if($setting['vers_op'] == 'ocstore3' || $setting['vers_op'] == 'opencart3'){
					$sql = "INSERT INTO ".$table." SET keyword = '".$category['seo_url']."', language_id=".$category['language_id'].", query = '".$prefix.$category['category_id']."'";
					$this->db->query($sql);
				}else{
					$sql = "INSERT INTO ".$table." SET keyword = '".$category['seo_url']."', query = '".$prefix.$category['category_id']."'";
					#$this->wtfarrey($sql);
					$this->db->query($sql);
				}

			}
		}

	}elseif($data['operator'] == 'manuf'){

		//префикс запрорса.
		$prefix = "manufacturer_id=";
		//опередяем какие производителей обновлять.  manufacturer_id
		$manufs = $this->db->query("SELECT ".DB_PREFIX."product.manufacturer_id, ".DB_PREFIX."manufacturer.name 
																FROM ".DB_PREFIX."product INNER JOIN ".DB_PREFIX."manufacturer 
																ON ".DB_PREFIX."product.manufacturer_id = ".DB_PREFIX."manufacturer.manufacturer_id
																WHERE ".DB_PREFIX."product.product_id IN (".$prs_id.")");
		$manufs = $manufs->rows;
		$manufs = array_unique($manufs, SORT_REGULAR);
		#$this->wtfarrey($manufs);
		foreach($manufs as $manuf){
			#$this->wtfarrey($manuf);
			//преобразовываем имя в seo_url
			$manuf['seo_url'] = $this->db->escape(substr($this->madeUrl($manuf['name']), 0, 255));
			unset($manuf['name']);

			//Удаляем запись если такая была ранние
			$this->db->query("DELETE FROM ".$table." WHERE query ='".$prefix.$manuf['manufacturer_id']."'");

			//проверяем есть ли такой url в базе.
			$count_mf = $this->db->query("SELECT COUNT(*) as count FROM ".$table." WHERE keyword = '".$manuf['seo_url']."'");

			//если такой юрл есть то добавляем в конце id товара
			if($count_mf->row['count']){
				//получаем длину id товара. И отнимаем ее от 255 что бы добавить туда id товара
				$strlen = (255 - (strlen($manuf['manufacturer_id']) + 1));
				$manuf['seo_url'] = substr($manuf['seo_url'], 0, $strlen).'-'.$manuf['manufacturer_id'];
			}
			
			//выполняем запрос на добавление seo url товара
			if($setting['vers_op'] == 'ocstore3' || $setting['vers_op'] == 'opencart3'){
				//заполняем для всех языков
				$manuf_langs = explode(',', $langs_id);
				foreach($manuf_langs as $manuf_lang){
					$sql = "INSERT INTO ".$table." SET keyword = '".$manuf['seo_url']."', language_id=".$manuf_lang.", query = '".$prefix.$manuf['manufacturer_id']."'";
					$this->db->query($sql);
				}
			}else{
				$sql = "INSERT INTO ".$table." SET keyword = '".$manuf['seo_url']."', query = '".$prefix.$manuf['manufacturer_id']."'";
				$this->db->query($sql);
			}
		}
	}
}

//использовать поиск замену
public function toolFindReplace($data, $prs_id, $dn_id){

	//проверяем есть ли правила.
	if(!empty($data['rules'])){

		//преобразуем правила. 
		$data['rules'] = $this->parseRulesToReplace($data);
		#$this->wtfarrey($data['rules']);

		//проверяем что бы правила поиск замены не были пусты.
		if(!empty($data['rules'])){

			//проверяем что бы было выбрано поле.
			if($data['operator'] == 'product_name'){

				//получаем данные о товаре в котором будем работать.
				$products = $this->db->query("SELECT product_id, language_id, name FROM ".DB_PREFIX."product_description WHERE product_id IN (".$prs_id.")");

				//проверяем что бы под фильтр попали товары.
				if($products->num_rows > 0){

					foreach($products->rows as $product){

			  		foreach($data['rules'] as $rule){

			  			if(isset($rule[0]) && isset($rule[1])){

			  				$rule[0] = $this->pregRegLeft($rule[0]);
			  				$rule[1] = $this->pregRegRight($rule[1]);
			  				$product['name'] = preg_replace($rule[0], $rule[1], html_entity_decode($product['name']));
			  			}
			  		}

			  		//Записываем рузультат.
			  		$this->db->query("UPDATE ".DB_PREFIX."product_description SET 
			  			name ='".$this->db->escape($product['name'])."' WHERE product_id =".$product['product_id']." AND language_id=".(int)$product['language_id']);
			  	}
		  	}

			}elseif($data['operator'] == 'product_desc'){

				//получаем данные о товаре в котором будем работать.
				$products = $this->db->query("SELECT product_id, language_id, description FROM ".DB_PREFIX."product_description WHERE product_id IN (".$prs_id.")");
				//проверяем что бы под фильтр попали товары.
				if($products->num_rows > 0){
					
					foreach($products->rows as $product){

			  		foreach($data['rules'] as $rule){

			  			if(isset($rule[0]) && isset($rule[1])){

			  				$rule[0] = $this->pregRegLeft($rule[0]);
			  				$rule[1] = $this->pregRegRight($rule[1]);
			  				$product['description'] = preg_replace($rule[0], $rule[1], html_entity_decode($product['description']));
			  			}
			  		}

			  		//Записываем рузультат.
			  		$this->db->query("UPDATE ".DB_PREFIX."product_description SET 
			  			description ='".$this->db->escape($product['description'])."' WHERE product_id =".$product['product_id']." AND language_id=".(int)$product['language_id']);
			  	}
				}
			}
		}
	}
}

//удалить товар
public function toolDelProducts($data, $prs_id, $dn_id){
	//получаем глобальные настройки. И определяем какой движок используется.
	$setting = $this->getSetting($dn_id);

	//Из за разности движков определяем в какой таблице нужно работать.
	$table = '';
	if($setting['vers_op'] == 'ocstore2' || $setting['vers_op'] == 'opencart2'){
		$table = DB_PREFIX."url_alias"; 
	}elseif($setting['vers_op'] == 'ocstore3' || $setting['vers_op'] == 'opencart3'){
		$table = DB_PREFIX."seo_url"; 
	}

	//Определяем еть что удалять или нет.
	if($prs_id){

		// опередляем что нам удалять только товары или товары с фото.
		// 1 - удаляем только товары. | 2 - Удаляем товары и их фото
		if($data['operator'] == 2){

			//Получаем путь к главному фото.
			$main_imgs = $this->db->query("SELECT image FROM ".DB_PREFIX."product WHERE product_id IN (".$prs_id.")");

			//Удаляем главное фото товара.
			foreach($main_imgs->rows as $img_main){
				@unlink(DIR_IMAGE.$img_main['image']);
			}
			//Для экономия места удаляем массив.
			unset($main_imgs);

			//получаем сиписок доп фото
			$imgs = $this->db->query("SELECT image FROM ".DB_PREFIX."product_image WHERE product_id IN (".$prs_id.")");

			//Удаляем доп фото
			foreach($imgs->rows as $img){
				@unlink(DIR_IMAGE.$img['image']);
			}
			unset($imgs);
		}
		
		//Удаляем товар из главной таблицы.
		$this->db->query("DELETE FROM ".DB_PREFIX."product WHERE product_id IN (".$prs_id.")");
		//Удаляем из описания товара
		$this->db->query("DELETE FROM ".DB_PREFIX."product_description WHERE product_id IN (".$prs_id.")");
		//Удаляем из атрибутов.
		$this->db->query("DELETE FROM ".DB_PREFIX."product_attribute WHERE product_id IN (".$prs_id.")");
		//Удаляем дисконты
		$this->db->query("DELETE FROM ".DB_PREFIX."product_discount WHERE product_id IN (".$prs_id.")");
		//Удаляем фильтры
		$this->db->query("DELETE FROM ".DB_PREFIX."product_filter WHERE product_id IN (".$prs_id.")");
		//Удаляем записи доп фото 
		$this->db->query("DELETE FROM ".DB_PREFIX."product_image WHERE product_id IN (".$prs_id.")");
		//Удаляем опуции
		$this->db->query("DELETE FROM ".DB_PREFIX."product_option WHERE product_id IN (".$prs_id.")");
		//Удаляем значения опций 
		$this->db->query("DELETE FROM ".DB_PREFIX."product_option_value WHERE product_id IN (".$prs_id.")");
		//Что то еше  :) 
		$this->db->query("DELETE FROM ".DB_PREFIX."product_recurring WHERE product_id IN (".$prs_id.")");
		$this->db->query("DELETE FROM ".DB_PREFIX."product_related WHERE product_id IN (".$prs_id.")");
		$this->db->query("DELETE FROM ".DB_PREFIX."product_reward WHERE product_id IN (".$prs_id.")");
		$this->db->query("DELETE FROM ".DB_PREFIX."product_special WHERE product_id IN (".$prs_id.")");
		//Удаляем записи товара в категории
		$this->db->query("DELETE FROM ".DB_PREFIX."product_to_category WHERE product_id IN (".$prs_id.")");
		//Удаляем файлы 
		$this->db->query("DELETE FROM ".DB_PREFIX."product_to_download WHERE product_id IN (".$prs_id.")");
		//Удаляем расположение.
		$this->db->query("DELETE FROM ".DB_PREFIX."product_to_layout WHERE product_id IN (".$prs_id.")");
		//Удаляем присвоение товара магазину.
		$this->db->query("DELETE FROM ".DB_PREFIX."product_to_store WHERE product_id IN (".$prs_id.")");
		
		//Формируем массив для удаления SEO_URL
		$tmp_prs_id = explode(',', $prs_id);
		$seo_pr_id = '';
		foreach($tmp_prs_id as $key_seo => $pr_id){
			if($key_seo == 0){ $seo_pr_id = "'product_id=".$pr_id."'"; } else { $seo_pr_id .= ",'product_id=".$pr_id."'"; }
		}
		
		$this->db->query("DELETE FROM ".$table." WHERE query IN (".$seo_pr_id.")");
	}
}

//Фунция удваления фото товара
public function toolDelProductsImg($data, $prs_id, $dn_id){
	#$this->wtfarrey($prs_id);
	//получаем главное фото.
	$main_imgs = $this->db->query("SELECT image FROM ".DB_PREFIX."product WHERE product_id IN (".$prs_id.")");
	//Удаляем главное фото товара.
	foreach($main_imgs->rows as $img_main){
		@unlink(DIR_IMAGE.$img_main['image']);
	}
	//Для экономия места удаляем массив.
	unset($main_imgs);

	//обновляем запись в базе что фото были удалены.
	$this->db->query("UPDATE ".DB_PREFIX."product SET image = '' WHERE product_id IN (".$prs_id.")");

	//получаем сиписок доп фото
	$imgs = $this->db->query("SELECT image FROM ".DB_PREFIX."product_image WHERE product_id IN (".$prs_id.")");
	//Удаляем доп фото
	foreach($imgs->rows as $img){
		@unlink(DIR_IMAGE.$img['image']);
	}
	unset($imgs);

	//Удаляем запись в базе про доп фото, мы же их удалили.
	$this->db->query("DELETE FROM `".DB_PREFIX."product_image` WHERE `product_id` IN (".$prs_id.")");
}

//удаление акции в товаре.
public function toolDelPriceSpec($data, $prs_id, $dn_id){
	//Удаляем запись в базе про акцию в товарах.
	$this->db->query("DELETE FROM `".DB_PREFIX."product_special` WHERE `product_id` IN (".$prs_id.")");
}

//Удаление атрибутов в товаре.
public function toolDelAttrProducts($data, $prs_id, $dn_id){
	#Удаляем все атрибуты из товара.
	$this->db->query("DELETE FROM `" . DB_PREFIX . "product_attribute` WHERE `product_id` IN (".$prs_id.")");
}

//Удаление опций в товаре.
public function toolDelOptProducts($data, $prs_id, $dn_id){
	$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option_value` WHERE `product_id` IN (".$prs_id.")");
	$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option` WHERE `product_id` IN (".$prs_id.")");
}

//Фунция получения списка id товаров.
public function toolGetPrsId($data){
	#$this->wtfarrey($data);
	$where = ' WHERE';
	#$list_id = '1';
	//Проверяем выбранного поставшика.
	if(!empty($data['dn_id'])){
		$where .= " ".DB_PREFIX."product.dn_id = '".(int)$data['dn_id']."'";
	}else{
		$where .= " ".DB_PREFIX."product.dn_id >= 0";
	}

	//Проверяем есть ли фильтр по производителям.
	if(!empty($data['manufs'])){
		$where .= " AND ".DB_PREFIX."product.manufacturer_id in (".implode(',', $data['manufs']).")";
	}
	//Проверяем есть ли фильтр по производителям.
	if(!empty($data['cats'])){
		$where .= " AND ".DB_PREFIX."product_to_category.category_id in (".implode(',', $data['cats']).")";
		$inner_cats =" INNER JOIN ".DB_PREFIX."product_to_category ON ".DB_PREFIX."product.product_id = ".DB_PREFIX."product_to_category.product_id";
	}else{
		$inner_cats = "";
	}
	//Проверяем есть ли фильтр по языку.
	if(!empty($data['langs'])){
		$where .= " AND ".DB_PREFIX."product_description.language_id in (".implode(',', $data['langs']).")";
	}

	if(!empty($data['filters'])){
		//перебераем все фильтры
		foreach ($data['filters'] as $filter) {

			//для начала проверяем есть ли значение в этой фильтре
			if(empty($filter['value']) && $filter['value'] != '0'){
				$filter['value'] = '';
			}
				
				//определяем поле в таблице.
				if($filter['take_filtr'] == '0'){
					continue;
				}elseif($filter['take_filtr'] == 'product_id'){ 

					$table = DB_PREFIX.'product.product_id';

				}elseif($filter['take_filtr'] == 'sku'){

					$table = DB_PREFIX.'product.sku';

				}elseif($filter['take_filtr'] == 'model'){

					$table = DB_PREFIX.'product.model';
				
				}elseif($filter['take_filtr'] == 'price'){
				
					$table = DB_PREFIX.'product.price';
				
				}elseif($filter['take_filtr'] == 'quantity'){
				
					$table = DB_PREFIX.'product.quantity';
				
				}elseif($filter['take_filtr'] == 'status'){
				
					$table = DB_PREFIX.'product.status';
				
				}elseif($filter['take_filtr'] == 'date_added'){
				
					//проверяем и преобразовываем дату
					$filter['value'] = str_replace('{date}', date("Y-m-d"), $filter['value']);
					$table = DB_PREFIX.'product.date_added';
				
				}elseif($filter['take_filtr'] == 'date_modified'){
				
					//проверяем и преобразовываем дату
					$filter['value'] = str_replace('{date}', date("Y-m-d"), $filter['value']);
					$table = DB_PREFIX.'product.date_modified';
				
				}elseif($filter['take_filtr'] == 'name'){
				
					$table = DB_PREFIX.'product_description.name';
				
				}elseif($filter['take_filtr'] == 'description'){
				
					$table = DB_PREFIX.'product_description.description';
				
				}elseif($filter['take_filtr'] == 'upc'){
				
					$table = DB_PREFIX.'product.upc';
				
				}elseif($filter['take_filtr'] == 'ean'){
				
					$table = DB_PREFIX.'product.ean';
				
				}elseif($filter['take_filtr'] == 'jan'){
				
					$table = DB_PREFIX.'product.jan';
				
				}elseif($filter['take_filtr'] == 'isbn'){
				
					$table = DB_PREFIX.'product.isbn';
				
				}elseif($filter['take_filtr'] == 'mpn'){
				
					$table = DB_PREFIX.'product.mpn';
				
				}elseif($filter['take_filtr'] == 'location'){
				
					$table = DB_PREFIX.'product.location';
				
				}

				#$this->wtfarrey($filter);
				$pos = $this->toolFilterPosition($filter['position'], $filter['style']);

				//делим значение на массив если ли это многомерное значение. Заодно удаляем пустые массивы
				$filter['value'] = explode('|', $filter['value']);
				foreach($filter['value'] as $key_value => $value){

					$value = $this->db->escape($value);
					if($key_value == 0){ 
						$where .= ' AND ('.$table.str_replace('{data}', $value, $pos); 
					}else{
						$where .= ' OR '.$table.str_replace('{data}', $value, $pos);
					}
				}
				$where .= ')';

			
		}
	}

	$language_id = 1;
	//главный запрос на получение id товара.
	$sql = "SELECT ".DB_PREFIX."product.product_id 
	FROM ".DB_PREFIX."product INNER JOIN ".DB_PREFIX."product_description 
	ON ".DB_PREFIX."product.product_id = ".DB_PREFIX."product_description.product_id".$inner_cats.$where;

	#$this->wtfarrey($sql);

	$prs_id = $this->db->query($sql);
	$prs_id = $prs_id->rows;
	$prs_id = array_unique($prs_id, SORT_REGULAR);

	$list_id = '';
	foreach($prs_id as $key => $pr){

		if($key){ $list_id .= ','.$pr['product_id']; } else { $list_id = $pr['product_id'];}

	}

	if(empty($list_id)){ $list_id = 0;}

	return $list_id;
}

//тестовая фунция получение товара.
public function toolFilterToPage($data, $dn_id){
	#$this->wtfarrey($data);
	$products = [];
	$back_cod = [];
	$answ = [];
	$page = 1;
	$page_count = 50;
	if(!empty($data['page'])){ $page = $data['page']; }
	if(!empty($data['page_count'])){ $page_count = $data['page_count']; }

	//получаем основной язык админки.
	$language_id = $this->getLangDef();

	//определяем колво товаров на страницу
	$limit_start = ($page * $page_count) - $page_count;
	$limit_stop = $limit_start + $page_count;
	$limit = ' LIMIT '.$limit_start.','.$limit_stop;
	#$this->wtfarrey($limit);

	//получаем список id товаров которые попадают под фильтры
	$prs_id = $this->toolGetPrsId($data);

	//Получаем список товаров
	//////////////////////////////

	//Получаем колво товаров.
	$total_products = $this->db->query("SELECT COUNT(*) as count FROM ".DB_PREFIX."product WHERE product_id IN (".$prs_id.")");
	$total_products = $total_products->row['count'];


	$sql = "SELECT ".DB_PREFIX."product.product_id, ".DB_PREFIX."product.model, ".DB_PREFIX."product.sku, ".DB_PREFIX."product.price, ".DB_PREFIX."product.quantity, ".DB_PREFIX."product.image, ".DB_PREFIX."product.status, ".DB_PREFIX."product.date_added, ".DB_PREFIX."product.date_modified, ".DB_PREFIX."product.dn_id, ".DB_PREFIX."product_description.name FROM ".DB_PREFIX."product INNER JOIN ".DB_PREFIX."product_description ON ".DB_PREFIX."product.product_id = ".DB_PREFIX."product_description.product_id 
		WHERE ".DB_PREFIX."product.product_id in (".$prs_id.") AND ".DB_PREFIX."product_description.language_id = ".$language_id.$limit;

	#$this->wtfarrey($sql);

	$back_cod['sql'] = $sql;

	$products = $this->db->query($sql);
	$products = $products->rows;
	$products = array_unique($products, SORT_REGULAR);
	//преобразовываем фото
	foreach($products as &$product){
		$product['url_out'] = HTTP_CATALOG.'index.php?route=product/product&product_id='.$product['product_id'];
		$product['image'] = $this->toolResizeImg($product['image']);
		#$product['description'] = htmlspecialchars($product['description']);
		if($product['status']) { $product['status'] = 'Вкл (1)';}else{ $product['status'] = 'Выкл (0)';}
	}


	$answ['products'] = $products;
	$answ['back_cod'] = $back_cod['sql'];
	$answ['total'] = $total_products;
	#$this->wtfarrey($answ);
	#$this->wtfarrey($products);
	return $answ;
}
############################################################################################
############################################################################################
#						CRON!!!
############################################################################################
############################################################################################

//Сохранение настроек
public function saveFormCron($data){
	#$this->wtfarrey($data);

	//Обновляем инфу по времени крона.
	if(empty($data['timezone'])){ $data['timezone'] = '+0';} 
		$this->db->query("UPDATE `".DB_PREFIX."pars_cron` SET timezone ='".$this->db->escape($data['timezone'])."'");

	//обновляем информацию о заданиях.
	foreach($data['cron_list'] as $cron){

		//приводим в поряд даты перед сохранением.
		$cron['time_day'] = preg_replace('#[^0-9-*]#', '', $cron['time_day']);
		$cron['time_week'] = preg_replace('#[^0-9-*]#', '', $cron['time_week']);
		$cron['time_hour'] = preg_replace('#[^0-9-*]#', '', $cron['time_hour']);
		if(empty($cron['pause'])){ $cron['pause'] = 0;}
		if(empty($cron['timeout']) && $cron['timeout'] != '0'){ $cron['timeout'] = 4;}
		if(empty($cron['cache_page'])){ $cron['cache_page'] = 0;}

		$this->db->query("UPDATE `".DB_PREFIX."pars_cron_list` SET 
			`on`='".$this->db->escape($cron['on'])."',
			`timeout`='".$this->db->escape($cron['timeout'])."',
			`time_day`='".$this->db->escape($cron['time_day'])."',
			`time_week`='".$this->db->escape($cron['time_week'])."',
			`time_hour`='".$this->db->escape($cron['time_hour'])."',
			`thread`='".$this->db->escape($cron['thread'])."',
			`pause`='".$this->db->escape($cron['pause'])."',
			`cache_page`='".$this->db->escape($cron['cache_page'])."',
			`sort`='".$this->db->escape($cron['sort'])."' 
			WHERE id = '".(int)$cron['id']."'
			");


		//удаляем сушествующие записи на крон.
		$this->db->query("DELETE FROM `".DB_PREFIX."pars_cron_tools` WHERE task_id = ".$cron['id']);

		//запись доп заданий.
		if(!empty($cron['ptts'])){

			//перебераем все задания и записываем в базу. 
			foreach($cron['ptts'] as $ptts){

				$this->db->query("INSERT INTO `".DB_PREFIX."pars_cron_tools` SET 
					task_id=".(int)$cron['id'].", 
					pt_id =".(int)$ptts['pt_id'].", 
					when_do =".(int)$ptts['when_do']);

			}
		}
	}
}

//Получаем настройки стрницы крон.
public function getCronPageInfo(){
	
	//статус крона
	$cron_main = $this->getCronMain();

	//получаем данные с крона, точнее список заданий
	$crons = $this->db->query("SELECT * FROM `".DB_PREFIX."pars_cron_list` ORDER BY id ASC");
	$data['crons'] = $crons->rows;
	#$this->wtfarrey($data['crons']);
	$action['pr_grab']  = [0 => 'Не очишать Ссылки на товары', 1 => 'Удалить Ссылки на товары перед началом сбора'];
	$action['pr_csv'] = [0 => 'Не удалять прайс лист', 1 => 'Удалить прайс лист в начала работы крона'];
	$action['pr_im']  = [1 => 'Добавлять', 2 => 'Обновлять', 3 => 'Добавлять и обновлять'];
	$task = [1 => 'Сбор ссылок', 2 => 'Парсинг в CSV', 3 => 'Парсинг в ИМ', 4 => 'Парсинг в кэш', 0 => 'Задание без парсинга'];

	//получаем список всех тулсов.
	$data['patterns'] = $this->cronGetAllPatterns();
	#$this->wtfarrey($data['patterns']);
	$data['patterns_json'] = json_encode($data['patterns']);

	//приводим в порядок данные.
	#$this->wtfarrey($data['crons']);
	foreach ($data['crons'] as &$cron) {
		#$this->wtfarrey($cron);

		//задание приводим в порядок.
		$cron['task_name'] = $task[$cron['task']];

		//действия приводим в порядок.
		if($cron['task'] == 1){
			$cron['action_name'] = $action['pr_grab'][$cron['action']];
		}elseif($cron['task'] == 2){
			$cron['action_name'] = $action['pr_csv'][$cron['action']];
		}elseif($cron['task'] == 3){
			$cron['action_name'] = $action['pr_im'][$cron['action']];
		}else{
			$cron['action_name'] = '';
		}

		//информация для таблицы

		//Колока времени запуска.
		$cron['table_time_srt'] = $this->cronMadeTimeToTable($cron);
		
		//колонка состояния
		$cron['table_on'] = '';
		if($cron['on']){
			$cron['table_on'] = '<span class="text-success"><b>Вкл</b></span>';
		}else{
			$cron['table_on'] = '<span class="text-warning"><b>Выкл</b></span>';
		}

		//Колона информации тайм аута.
		$cron['table_timeout'] = 'Не блокирует';
		if($cron['time_end'] !=0){
			$check_time_end = $cron['time_end'] + $cron['timeout'] * 60**2;
			//делаем сравнение.
			if($cron['status'] != 'run' && time() < $check_time_end){
				$cron['table_timeout'] = '<span class="text-danger"><b>'.gmdate("H:i:s", $check_time_end+$cron_main['timezone']).'</b></span>';
			}

		}

		//Колонка информации
		#$this->wtfarrey($cron['time_end']);
		#$this->wtfarrey($cron_main['timezone']);
		if(!empty($cron['time_end'])){ 
			$cron['time_end'] = gmdate("Y-m-d H:i:s", $cron['time_end']+$cron_main['timezone']); 
		}else {
			$cron['time_end'] = '';
		}

		$cron['table_info'] = '';
		if($cron['status'] == 'end' && (empty($cron['time_end']))){
			$cron['table_info'] = 'Ожидает первый запуск';
		}elseif($cron['status'] == 'end' && (!empty($cron['time_end']))) {
			$cron['table_info'] = 'Выполнено '.$cron['time_end'];
		}elseif($cron['status'] == 'run'){
			$cron['table_info'] = '<span class="text-danger"><b>Ожидает завершения</b></span>';
		}

		//информация по ссылкам
		if($cron['task'] == '1'){

			$cron['table_link_stat'] = '';
			$table_link_done = $this->db->query("SELECT COUNT(id) as count FROM `".DB_PREFIX."pars_sen_link` WHERE `dn_id`=".(int)$cron['dn_id']." AND scan_cron = 0");
			$table_link_done = $table_link_done->row['count'];
			$table_link_wait = $this->db->query("SELECT COUNT(id) as count FROM `".DB_PREFIX."pars_sen_link` WHERE `dn_id`=".(int)$cron['dn_id']." AND scan_cron = 1");
			$table_link_wait = $table_link_wait->row['count'];

			$cron['table_link_stat'] = '<span class="text-success"><b>'.$table_link_done.'</b></span> / <span class="text-warning"><b>'.$table_link_wait.'</b></span> | '.($table_link_done+$table_link_wait);

		}else{
			$cron['table_link_stat'] = '';
			$table_link_done = $this->db->query("SELECT COUNT(id) as count FROM `".DB_PREFIX."pars_link` WHERE `dn_id`=".(int)$cron['dn_id']." AND scan_cron = 0");
			$table_link_done = $table_link_done->row['count'];

			$table_link_wait = $this->db->query("SELECT COUNT(id) as count FROM `".DB_PREFIX."pars_link` WHERE `dn_id`=".(int)$cron['dn_id']." AND scan_cron = 1");
			$table_link_wait = $table_link_wait->row['count'];

			$cron['table_link_stat'] = '<span class="text-success"><b>'.$table_link_done.'</b></span> / <span class="text-warning"><b>'.$table_link_wait.'</b></span> | '.($table_link_done+$table_link_wait);
		}
		//подготавливаем данные для доп заданий.
		#$this->wtfarrey($cron['tools']);
		$cron['tools'] = $this->db->query("SELECT * FROM `".DB_PREFIX."pars_cron_tools` WHERE `task_id`=".(int)$cron['id']." ORDER BY id");
		//записываем колво задаений
		$tools_last_id = $cron['tools']->num_rows;
		
		$cron['tools'] = $cron['tools']->rows;
		
		#$this->wtfarrey($cron['tools']);

		$data['tools_last_key'][$cron['id']] = $tools_last_id;
	}


	//Кнопка включения выключения крона
	if($cron_main['permit'] == 'run') {
		$data['cron_button']['text'] = 'Крон включен => Отключить';
		$data['cron_button']['class'] = 'btn btn-success';
	} else {
		$data['cron_button']['text'] = 'Крон отключен => Включить';
		$data['cron_button']['class'] = 'btn btn-danger';
	}

	$data['cron_permit'] = $cron_main['permit'];

	$dn_list = $this->db->query("SELECT `dn_id`, `dn_name` FROM `".DB_PREFIX."pars_setting`");
	$data['dn_list'] = array_column($dn_list->rows, 'dn_name', 'dn_id');
	
	//Время сайта
	$data['time_machin'] = '<samp class="text-warning">'.gmdate("H:i:s", time()).'</samp>';
	if($cron_main['timezone'] != '+0'){
		$data['time_machin'] = '<samp class="text-success">'.gmdate("H:i:s", time()+$cron_main['timezone']).'</samp>';
	}
	//Время которое выбрал пользователь.
	$data['select_time'] = $cron_main['timezone'];
	//Создаем массив с временными зонами пользователей.
	$data['user_times'] = [
			                    "+0" => 'Выбор часового пояса',
			                    "+3600" => gmdate('H:i:s', time() + 3600),
			                    "+7200" => gmdate('H:i:s', time() + 7200),
			                    "+10800" => gmdate('H:i:s', time() + 10800),
			                    "+14400" => gmdate('H:i:s', time() + 14400),
			                    "+18000" => gmdate('H:i:s', time() + 18000),
			                    "+21600" => gmdate('H:i:s', time() + 21600),
			                    "+25200" => gmdate('H:i:s', time() + 25200),
			                    "+28800" => gmdate('H:i:s', time() + 28800),
			                    "+32400" => gmdate('H:i:s', time() + 32400),
			                    "+36000" => gmdate('H:i:s', time() + 36000),
			                    "+39600" => gmdate('H:i:s', time() + 39600),
			                    "+43200" => gmdate('H:i:s', time() + 43200),
			                    "-3600" => gmdate('H:i:s', time() - 3600),
			                    "-7200" => gmdate('H:i:s', time() - 7200),
			                    "-10800" => gmdate('H:i:s', time() - 10800),
			                    "-14400" => gmdate('H:i:s', time() - 14400),
			                    "-18000" => gmdate('H:i:s', time() - 18000),
			                    "-21600" => gmdate('H:i:s', time() - 21600),
			                    "-25200" => gmdate('H:i:s', time() - 25200),
			                    "-28800" => gmdate('H:i:s', time() - 28800),
			                    "-32400" => gmdate('H:i:s', time() - 32400),
			                    "-36000" => gmdate('H:i:s', time() - 36000),
			                    "-39600" => gmdate('H:i:s', time() - 39600),
                    			"-43200" => gmdate('H:i:s', time() - 43200),
												];
	

	//переводим в json ключи заданий
	//если еше нет заданий
	if(empty($data['tools_last_key'])){
		$data['tools_last_key'] = json_encode($data['tools_last_key'][0] = 0);
	}else{
		$data['tools_last_key'] = json_encode($data['tools_last_key']);
	}
	#$this->wtfarrey($data);
	return $data;
}

//добавить задание
public function cronAddTask($data){

	//приводим в порядок данные для создания крон задачи.
	$dn_id = (int)$data['cron_add_dn'];
	$task = (int)$data['cron_add_task'];
  $action_1 = (int)$data['cron_add_action_1'];
  $action_2 = (int)$data['cron_add_action_2'];
  $action_3 = (int)$data['cron_add_action_3'];
  $action = 0;
  
  $permit = 1;

  if($task == 1){
  	$action = $action_1;
  } elseif($task == 2) {
  	$action = $action_2;
  } elseif($task == 3){
  	$action = $action_3;
  }

  if(empty($dn_id)){
  	$permit = 0;
  	$this->session->data['error'] = ' Не выбран проект для создания задания!';
  }elseif(empty($task)){
  	$permit = 0;
  	$this->session->data['error'] = ' Не выбрано задание для проекта!';
  }
  
	//Создаем пустую болванку.
	if($permit){
		$this->db->query("INSERT INTO `".DB_PREFIX."pars_cron_list` SET `dn_id`='".$dn_id."', `task`='".$task."', `action`='".$action."'");
	}
}

//Удаление задание
public function cronDelTask($data){
	#$this->wtfarrey($data);
	$this->db->query("DELETE FROM `".DB_PREFIX."pars_cron_list` WHERE `id` = '".(int)$data['task_del']."'");
	$this->db->query("DELETE FROM `".DB_PREFIX."pars_cron_tools` WHERE `task_id` = '".(int)$data['task_del']."'");
}

//Приведения времени к определенному формату.
public function preparinTimeToCron($str){
	$time = ['0' => 0, '1' => 60];
	//если время не равно * тогда начинаем колдавать.
	if($str != '*'){
		//делим строку на массив.
		$time = explode('-', $str);
		//на вский случай приводим к числу
		$time[0] = (int)$time[0];
		//ключ 0 нижная граница ключ 1 верхняя, если вернхей нету значит она равна нижней
		if(empty($time[1])){ $time[1] = $time[0]+1; }
	}
	return $time;
}

//Проверяем пришло ли время выполнять задание, или нет.
public function chackTimeToCron($task, $main_cron){
	$answer = 0;
	$time_server['now'] = time()+$main_cron['timezone'];
	$task['time_end'] = $task['time_end']+$main_cron['timezone'];
	$time_server['time_day'] = gmdate('d', $time_server['now']);
	$time_server['time_week'] = gmdate('N', $time_server['now']);
	$time_server['time_hour'] = gmdate('H', $time_server['now']);
	
	#$this->wtfarrey($time_server);
	#$this->wtfarrey($task);

	if( ($time_server['time_hour'] >= $task['time_hour'][0]) && ($time_server['time_hour'] < $task['time_hour'][1]) ){
		#$this->wtfarrey('час совпал');
		$answer = 1;
		#Дополнительно проверяем период.
		if($task['status'] == 'end' && ( ($time_server['now'] - $task['time_end']) < ($task['timeout'] * 60**2) ) ){
			$answer = 0;
		}
	}

	if( ($answer == 1) && ($time_server['time_week'] >= $task['time_week'][0]) && ($time_server['time_week'] < $task['time_week'][1]) ){
		$answer = 1;
		#$this->wtfarrey('неделя отработала');
	} else {
		$answer = 0;
	}

	if( ($answer == 1) && ($time_server['time_day'] >= $task['time_day'][0]) && ($time_server['time_day']< $task['time_day'][1]) ){
		$answer = 1;
		#$this->wtfarrey('День сработал');
	} else {
		$answer = 0;
	}

	#$this->wtfarrey($answer);
	return $answer;
}

//Точка входа крона
public function cronStart(){
	
	//Получяае получаем право на выполнение 
	$main_cron = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_cron");
	$main_cron = $main_cron->row;

	//блок отвечающий за отсеевание запроса на создание второго потока парсинга. 
	//так же данный блок фикси аварийное завершение скрипта со стороны хостинга. 
	if($main_cron['work'] > 0){

		//Получаем время сейчас что бы проверить не зависло ли значение.
		if( (time() - $main_cron['work']) > 300 ){ 
			$main_cron['work'] = 0;
		}else{
			echo "Запуск отменен, крон предполагает что один из процессов не завершен.<br>
			Если процесс парсинга не идет, а вы видите это сообщение, то возможно выполнение скрипта было остановлено аварийно.<br>
			Блокировка выполнение будет снята через <b style='color: #a94442;'>".gmdate("H:i:s", 300 - (time() - $main_cron['work']) ) ."</b>";
		}

	}

	if( !empty($main_cron) && ($main_cron['permit'] == 'run' && $main_cron['work'] == '0') ){

		//Получяае список актуальных заданий.
		$cron_list = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_cron_list WHERE `on` != 0 ORDER BY sort");
		$cron_list = $cron_list->rows;

		#$this->wtfarrey($cron_list);
		//проверяем все задания нужно ли что то делать.
		foreach($cron_list as $task){

			$task['time_day'] = $this->preparinTimeToCron($task['time_day']);
			$task['time_week'] = $this->preparinTimeToCron($task['time_week']);
			$task['time_hour'] = $this->preparinTimeToCron($task['time_hour']);

			//Получаем разрешение на работу.
			$task['permit'] = $this->chackTimeToCron($task, $main_cron);
			#$this->wtfarrey($task['permit']);

			//Время пришло подаван!
			if($task['permit']){
				$this->cronBlocking();
				$this->cronController($task);
			}

		}

	}

}
//Основная фунция выполнения крона. А точнее выполнение одного задания. 
public function cronController($task){
	#$this->wtfarrey($task);
	//Получаем ключевые значения.
	$dn_id = (int)$task['dn_id'];
	//Если парсинг в им то берем отдельный ннастройки. 
	if($task['task'] == 3){
		$setting = $this->getSettingToProduct($dn_id);
		//прави выбранное действие.
		$setting['action'] = $task['action'];
	}else{
		$setting = $this->getSetting($dn_id);
	}

	//проверяем первый запуск задания  или нет. 
	if($task['status'] == 'end'){
		//Выполнить фунцию подготовки к старту
		$first_start = $this->cronActivateTask($task);
	}

	//применяем отдельные настройки крона.
	$setting['thread'] = $task['thread'];
	$setting['pars_pause'] = $task['pause'];

	//Крути барабан. Фунция без остановочной работы.
	$off = 0;
	while($off == 0){

		//прроверяем не нажали ли стоп.
		$main_cron = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_cron");
		//Проверяем не остановил ли выполнение крона пользователь!
		if($main_cron->row['permit'] == 'stop'){ 
			$this->cronUnbloking();
			exit('Принудительная остановка выполнения крона!');
		} 

		# Типы заданий
		# 1 - Сбор ссылок | 2 - пасинг в csv | 3 - парсинг в ИМ | 4 - Прасинг в Кеш
		//Если задание связано с парсингом в csv
		if($task['task'] == 1){

			//проверяем первый запуск крона или нет. 0 - или нет переменной это не первый, 1 - первый
			if(!empty($first_start)){
				//ох и чихню я тут пишу :( 
				//Подставляем стартовую ссылку для начала сбора
				$urls[] = $setting['start_link'];
				//берем гланую ссылку и говорим модулю что это не первая итерация.
				$first_start = 0;

			}else{

				$links = $this->db->query("SELECT * FROM " . DB_PREFIX . "pars_sen_link WHERE scan_cron = 1 AND `dn_id`=".(int)$dn_id." LIMIT 0,50");
				
				if($links->num_rows > 0){
					//Блак многопоточности. берем нужное количество ссылок.
					$urls = [];
					foreach($links->rows as $key => $url){
						if($key < $setting['thread']){ $urls[] = $url['link']; } else { break; }
					}
				
				}else{
					//закончились ссылочки
					$this->cronTaskFinish($task);
					$off = 1;
					continue;
				}

			}

			//получаем настройки браузера.
		  $browser = $this->getBrowserToCurl($dn_id);
		  //Подменяем значения кеширования из настроек крона. 
		  $browser['cache_page'] = $task['cache_page'];

			//делаем мульти запрос
			$datas = $this->multiCurl($urls, $dn_id, $browser);
			//навсякий случай перед тем как начать работать с записью в файл проверяем что у нас есть на это время.
		  $this->cronChackTimeout();
			//Обрабатываем данные с мульти запроса. 
			foreach($datas as $key => $data){
				#помечаем ссылку как отсканированная
		   	$this->db->query("UPDATE ".DB_PREFIX."pars_sen_link SET scan_cron = 0 WHERE link='".$this->db->escape($data['url'])."' AND dn_id=".$dn_id);
		    	
		   	//производим зяпись лога курл, и паролельно проверяем нужно ли делать дальнейшую работу.
			 	$curl_error = $this->sentLogMultiCurl($data ,$dn_id);
			 	//если пришла ошибка заканчиваем эту итерацию и переходим к следующей.
	  		if($curl_error['error']){ 
	  			continue;
	  		}
			  		
				//передаем на обработку данные
				$this->ParsLink($data, $setting, $dn_id);
			}

			$this->cronChackTimeout();
		  $this->timeSleep($setting['pars_pause']);
		  $this->cronChackTimeout();

		}elseif($task['task'] == 2){

			//получаем ссылки для работы.
			$links = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_link WHERE dn_id=".(int)$dn_id." AND `scan_cron`=1 ORDER BY id ASC LIMIT 0,20");

			//если ссылки закончились заканчиваем этот балаган.
			if($links->num_rows == 0){ 
				//закончились ссылочки
				$this->cronTaskFinish($task);
				$off = 1;		
			} else {

				//собираем массив ссылок для мульти запроса.
		  	$urls = [];
		  	foreach($links->rows as $key => $url){
		  		if($key < $setting['thread']) {$urls[] = $url['link']; } else { break; }
		  	}

		  	//получаем настройки браузера.
		  	$browser = $this->getBrowserToCurl($dn_id);
		  	//Подменяем значения кеширования из настроек крона. 
		  	$browser['cache_page'] = $task['cache_page'];

		  	//делаем запрос.
		  	$datas = $this->multiCurl($urls, $dn_id, $browser);
		  	//навсякий случай перед тем как начать работать с записью в файл проверяем что у нас есть на это время.
		  	$this->cronChackTimeout();
		  	//Далее разбираем данные из мульти курла и делаем все нужные записи.
		  	foreach($datas as $key => $data){

					//производим зяпись лога курл, и паролельно проверяем нужно ли делать дальнейшую работу.
		  		$curl_error = $this->sentLogMultiCurl($data ,$dn_id);

					#помечаем ссылку как отсканированная
		    	$this->db->query("UPDATE ". DB_PREFIX ."pars_link SET `scan_cron`=0, error='".$curl_error['http_code']."' WHERE link='".$data['url']."' AND dn_id=".$dn_id);

		  		//если пришла ошибка заканчиваем эту итерацию и переходим к следующей.
		  		if($curl_error['error']){ 
		  			continue;
		  		}

		  		//Получаем разрешения на действия.
					if(!empty($setting['grans_permit'])){
						//плохая практика но что поделать, дергаем данные парсинга в ИМ
						$form = $this->preparinDataToStore($data, $dn_id);
						$permit_grans = $this->checkGransPermit($form, $setting, $dn_id);
						#$this->wtfarrey($permit_grans);
						//проверяем массив допуска и сравниваем с выбранным действием. 
						if( empty($permit_grans[4]['permit'])){ 
							$this->log('NoGranPermit', $permit_grans[4]['log'], $dn_id);
							continue; 
						}
					}

		  		$html = $data['content'];
		  		$csv = [];
		  		$csv = $this->changeDataToCsv($html, $data['url'], $dn_id);

		  		//Умышленно создаем маячек что нужно остановить загрузку прайса.
		  		$finish = 0;
		  		if($csv === false){ 
		  			$finish = 1; 
		  		} else {
		  			//преобразовывем данные для csv
		  			$csv['value'] = $this->transformCsv($csv['value']);
		  			//записываем данные в csv
		  			$this->createCsv($csv, $setting, $dn_id);
		  			
		  		}
		  	}

		  	//Если настройки csv несделаны делаем выход из фунции контроллер, тем самым передаем управление следуюшему заданию.
		  	//$finish может быть как 1 так 0 и даже не определена, проверять толко через empty
		  	if(!empty($finish)){
		  		return 1;			  		
		  	}

			}
		
		} elseif ($task['task'] == 4) {

			//пришел задание парсить в кеш.
			$links = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_link WHERE dn_id=".(int)$dn_id." AND `scan_cron`=1 ORDER BY id ASC LIMIT 0,20");

			#Если ссылок нету завершаем работу модуля.
		  if($links->num_rows == 0){
		  	//закончились ссылочки
				$this->cronTaskFinish($task);
		    $off = 1;

		  }else{

		  	//собираем массив ссылок для мульти запроса.
		  	$urls = [];
		  	foreach($links->rows as $key => $url){
		  		if($key < $setting['thread']) {$urls[] = $url['link']; } else { break; }
		  	}

		  	$browser = $this->getBrowserToCurl($dn_id);
		  	$browser['cache_page'] = 2;
		  	$datas = $this->multiCurl($urls, $dn_id, $browser);
		  	//навсякий случай перед тем как начать работать с записью в файл проверяем что у нас есть на это время.
		  	$this->cronChackTimeout();
		  	//Далее разбираем данные из мульти курла и делаем все нужные записи.
		  	foreach($datas as $link => $data){
		  		//производим зяпись лога курл, и паролельно проверяем нужно ли делать дальнейшую работу.
		  		$curl_error = $this->sentLogMultiCurl($data ,$dn_id);

					#помечаем ссылку как отсканированная
		    	$this->db->query("UPDATE ". DB_PREFIX ."pars_link SET `scan_cron`=0, error='".$curl_error['http_code']."' WHERE link='".$data['url']."' AND dn_id=".$dn_id);

		  		//если пришла ошибка заканчиваем эту итерацию и переходим к следующей.
		  		if($curl_error['error']){ 
		  			continue;
		  		}
					//Здесь должно что то делатся.
					//Но это кеш по этому ничего ;-)  		
		  	}
		  }

		} elseif ($task['task'] == 3) {

			//Парсинг в ИМ
			if($setting['sid'] == 'sku' && $setting['r_sku'] == 1){
	    	#$this->answjs('finish','ПАРСИНГ ОСТАНОВЛЕН : Нельзя обновлять значение которое является идентификатором товара. Измените действие в поле Артикул (sku)');
				return 'bad sku';
	  	}
	  	if($setting['sid'] == 'name' && $setting['r_name'] == 1){
	  	  #$this->answjs('finish','ПАРСИНГ ОСТАНОВЛЕН : Нельзя обновлять значение которое является идентификатором товара. Измените действие в поле Название');
	  	  return 'bad sku-name';
	  	}

			//Получаем списк неспарсенных ссылок.
			$links = $this->db->query("SELECT * FROM " . DB_PREFIX . "pars_link WHERE `dn_id`=".(int)$dn_id." AND `scan_cron`=1 ORDER BY id ASC LIMIT 0,20");

			//Проверяем закончился ли парсинг.
			if($links->num_rows == 0){
				//закончились ссылочки
				$this->cronTaskFinish($task);
				$off = 1;

			}else{

				//Блак многопоточности. берем нужное количество ссылок.
				$urls = [];
				foreach($links->rows as $key => $url){
					if($key < $setting['thread']){ $urls[] = $url['link']; } else { break; }
				}

				$browser = $this->getBrowserToCurl($dn_id);
		  	$browser['cache_page'] = $task['cache_page'];
		  	$datas = $this->multiCurl($urls, $dn_id, $browser);
		  	//навсякий случай перед тем как начать работать с записью в файл проверяем что у нас есть на это время.
		  	$this->cronChackTimeout();
				//перебераем данные с мулти запроса.
				foreach($datas as $key => $data){
					//производим зяпись лога курл, и паролельно проверяем нужно ли делать дальнейшую работу.
		  		$curl_error = $this->sentLogMultiCurl($data ,$dn_id);

					#помечаем ссылку как отсканированная
		    	$this->db->query("UPDATE ". DB_PREFIX ."pars_link SET `scan_cron`=0, error='".$curl_error['http_code']."' WHERE link='".$data['url']."' AND dn_id=".$dn_id);

		  		//если пришла ошибка заканчиваем эту итерацию и переходим к следующей.
		  		if($curl_error['error']){ 
		  			continue;
		  		}

					//Ссылка
					$link = $data['url'];
					//Прасим данные
					$form = $this->preparinDataToStore($data, $dn_id);
					#$this->wtfarrey($form);
					//Получаем разрешения на границы
					if(!empty($setting['grans_permit'])){
						$permit_grans = $this->checkGransPermit($form, $setting, $dn_id);

						//проверяем массив допуска и сравниваем с выбранным действием. 
						if($setting['action'] != 3 && empty($permit_grans[$setting['action']]['permit'])){ 
							$this->log('NoGranPermit', $permit_grans[$setting['action']]['log'], $dn_id);
							continue; 
						}
					}

					//Получаем разрешения на действия.
					$permit = $this->checkProduct($form, $setting, $link, $dn_id);

					//Проверка выбора действия.////////////////////
					// 1 -Добавлять | 2 - Обновлять | 3 - Добавлять и обновлять
					//////////////////////////////////////////////
					if($setting['action'] == 1){

						//провека допуска
						if($permit['add']['permit'] == 1){
							$this->addProduct($form, $link, $setting, $dn_id);
						}else{
							$log = ['sid' => $setting['sid'],	'sid_value' => $form[$setting['sid']],];
							$this->log('addProductIsTrue', $log, $dn_id);
						}

					}elseif($setting['action'] == 2){

						//провека допуска
						if($permit['up']['permit'] == 1){
							$this->updateProduct($form, $link, $setting, $dn_id, $permit['up']['pr_id']);
						}else{
							$log = [ 'sid' => $setting['sid'],	'sid_value' => $form[$setting['sid']], 'link' => $link ];
							#$this->wtfarrey($log);
							$this->log('NoFindProductToUpdate', $log, $dn_id);
						}

					}elseif($setting['action'] == 3){

						if($permit['add']['permit'] == 1){
							//проверка допуска страницы к добавлению товара, и включена ли проверка допуска
							if(!isset($permit_grans) || !empty($permit_grans[1]['permit'])){ 

								//провека допуска на добавление товара
								$this->addProduct($form, $link, $setting, $dn_id);

							}else{
								$this->log('NoGranPermit', $permit_grans[1]['log'], $dn_id);
							}

						}elseif($permit['up']['permit'] == 1){
							//проверка допуска страницы к обновлению товара, и включена ли проверка допуска
							if(!isset($permit_grans) || !empty($permit_grans[2]['permit'])){ 

								//проверка на обновление товара
								$this->updateProduct($form, $link, $setting, $dn_id, $permit['up']['pr_id']);

							}else{
								$this->log('NoGranPermit', $permit_grans[2]['log'], $dn_id);
							}

						}
					}

				}
			}
		}

		//Перед закрытием цикла while
		$this->cronChackTimeout();
		$this->timeSleep($setting['pars_pause']);
		$this->cronChackTimeout();
	}		

	#$this->wtfarrey($task);
}

//Остановка крона по времени. Буфер это значение насколько проверять.
public function cronChackTimeout($buf = 15){
	
	//Максимальное время выполенения скрипта. 
	$run_time['max'] = ini_get('max_execution_time');

	//проверка на вшивость хостинга
	if(empty($run_time['max'])){ 
		$run_time['max'] = 30;#если сервер не отдал значение, то по умолчанию 30с 
	}elseif ($run_time['max'] > 120) {
		$run_time['max'] = 60;
	} 
	
	//временная метка запуска скрипта. Запуска скрипта. 
		$run_time['start'] = $_SERVER['REQUEST_TIME'];
	$run_time['uptime'] = ( time() - $_SERVER['REQUEST_TIME']) + $buf; #зазор что бы веб сервер не успел выключить скрипт принудительно.

	//производим проверку на прирывание выполнения.
	if($run_time['uptime'] >= $run_time['max']){ 
		$this->cronRestart(); # Эта фунция запустит обработчик, и убьет процесс.
	}
}

//Фунция составления человеко понятной даты выполнения для таблицы
public function cronMadeTimeToTable($task){
	$str = [];
	$time['hour'] = $this->preparinTimeToCron($task['time_hour']);
	$time['day'] = $this->preparinTimeToCron($task['time_day']);
	$time['week'] = $this->preparinTimeToCron($task['time_week']);

	if($time['hour'][1] > 23){ $time['hour'][1] = 23;}
	if($time['day'][0] < 1){ $time['day'][0] = 1;}
	if($time['day'][1] > 31){ $time['day'][1] = 31;}
	if($time['week'][0] < 1){ $time['week'][0] = 1;}
	if($time['week'][1] > 7){ $time['week'][1] = 7;}

	if($time['hour'][0] == $time['hour'][1]){
		$str = 'В <b>'.$time['hour'][0].'</b>ч |';
	}else{
		$str = 'С <b>'.$time['hour'][0].'</b> до <b>'.$time['hour'][1].'</b>ч |';
	}

	if($time['week'][0] == $time['week'][1]){
		$str .= ' в <b>'.$time['week'][0].'</b>й день недели |';
	}else{
		$str .= ' с <b>'.$time['week'][0].'</b> по <b>'.$time['week'][1].'</b>й день недели |';
	}

	if($time['day'][0] == $time['day'][1]){
		$str .= ' в <b>'.$time['day'][0].'</b>й день месяца';
	}else{
		$str .= ' с <b>'.$time['day'][0].'</b> по <b>'.$time['day'][1].'</b>й день месяца';
	}

	return $str;
}

//Активация задания к выполнению
public function cronActivateTask($task){
	$time = time();
	$first_start = 0;
	//Дополнительные задания, если есть перед началом контролер выполнит.
	$this->cronToolsController($task, 1);

	//А теперь меняем статус задания
	$this->db->query("UPDATE `".DB_PREFIX."pars_cron_list` SET `status` = 'run', `time_end`='".$time."' WHERE `id`=".$task['id']);
	
	//опередяем тип задания и обнуляем список заданий. 
	if($task['task'] == 1){
		$this->db->query("UPDATE `".DB_PREFIX."pars_sen_link` SET `scan_cron` = 1 WHERE `dn_id` = ".$task['dn_id']);
	}else{
		$this->db->query("UPDATE `".DB_PREFIX."pars_link` SET `scan_cron` = 1 WHERE `dn_id` = ".$task['dn_id']);
	}

	//если задание парсинга в CSV проверяем нужно удалять прайс или нет. 
	if($task['task'] == 2 && $task['action'] == 1){

		$this->delFile($task['dn_id']);

	} elseif($task['task'] == 1 && $task['action'] == 1){ //если задание сбор ссылок и выставлено удалять ссылки удаляем.

		$this->DelParsLink($task['dn_id']);
		$first_start = 1;

	}

	return $first_start;
}

//фунция перезапуска крона.
public function cronRestart(){
	$url = HTTP_SERVER.'sp_cron.php';

	$uagent = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);     // возвращает веб-страницу
	curl_setopt($ch, CURLOPT_HEADER, 0);             // возвращает заголовки
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);     // переходит по редиректам
	curl_setopt($ch, CURLOPT_ENCODING, "");          // обрабатывает все кодировки | Проблемы в понимании этой опции. Отключил
	curl_setopt($ch, CURLOPT_USERAGENT, $uagent);    // useragent
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);    // таймаут соединения
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);            // таймаут ответа
	curl_setopt($ch, CURLOPT_MAXREDIRS, 3);          // останавливаться после 10-ого редиректа
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //Отключить проверку сертификата.

	//для минимальной задержки вставляю запись сюда.
	$this->cronUnbloking();

	curl_exec( $ch );
	curl_close( $ch );
	exit();
}

//Фунция завершения работы задания.
public function cronTaskFinish($task){

	$this->cronToolsController($task, 2);

	$time = time();
	//Ставит состояние старт заданию
	$this->db->query("UPDATE ".DB_PREFIX."pars_cron_list SET `time_end` = '".$this->db->escape($time)."', `status` = 'end' WHERE id = ".(int)$task['id']);
	$this->db->query("UPDATE ".DB_PREFIX."pars_cron_tools SET `scan` = 0 WHERE task_id = ".(int)$task['id']);
	$this->cronUnbloking();
}

//Включить выключить крон.
public function cronOnOff($data){
	#$this->wtfarrey($data);
	if($data['cron_permit'] == 'stop'){ 
		$data['cron_permit'] = 'run';
		$work = '0'; 
	} else { 
		$data['cron_permit'] = 'stop';
		$work = '1';
	}

	$this->db->query("UPDATE ".DB_PREFIX."pars_cron SET `permit` = '".$this->db->escape($data['cron_permit'])."', work = ".(int)$work);
}

//Статус крона.
public function getCronMain(){
	//статус крона
	$cron = $this->db->query("SELECT * FROM `".DB_PREFIX."pars_cron`");
	return $cron->row;
}

//принудительный рестарт задания от пользователя.
public function cronRestartTaskFromUser($task_id){
	#$this->wtfarrey($task_id);
	//Получаем данные об задании.
	$task = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_cron_list WHERE id =".(int)$task_id);
	$task = $task->row;

	//Помечаем задание как готовое к выполнению.
	$this->db->query("UPDATE ".DB_PREFIX."pars_cron_list SET status = 'end', time_end = '0' WHERE id =".(int)$task_id);

	//Помечаем все ссылки как не просканированные.
	if($task['task'] == 1){
		$this->db->query("UPDATE ".DB_PREFIX."pars_sen_link SET scan_cron = 1 WHERE dn_id =".(int)$task['dn_id']);
	}else{
		$this->db->query("UPDATE ".DB_PREFIX."pars_link SET scan_cron = 1 WHERE dn_id =".(int)$task['dn_id']);
	}
}

//Блокировка создания второго потока. Запрешаем выполнение пока work стоит 1
/*public function cronBlocking(){
	//запрос на блокировку процесса
	$this->db->query("UPDATE ".DB_PREFIX."pars_cron SET work = 1");
}*/

//Блокировка создания второго потока. Запрешаем выполнение пока work стоит 1
public function cronBlocking(){
	//запрос на блокировку процесса
	$this->db->query("UPDATE ".DB_PREFIX."pars_cron SET work = ".time());
}

//разблокировать выполнение крона.
public function cronUnbloking(){
	//запрос на разблокировку процесса
	$this->db->query("UPDATE ".DB_PREFIX."pars_cron SET work = 0");
}

//получение всех шаблонов заданий в формате Json | ПОВТОРНО НЕ ИСПОЛЬЗОВАТЬ!
public function cronGetAllPatterns(){
	$data = [];

	$patterns = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_tools_pattern");
	if($patterns->num_rows > 0){
		$data = $patterns->rows;
	}

	#$this->wtfarrey($json);
	return $data;
}

//преобразуем строку доп заданий в массив
public function cronMadeToolsArrey($str){
	$tools = explode(',', $str);
		
	//проверяем есть ли задания.
	if(!empty($tools[0])){
		
		foreach($tools as $key_last => $ptt){
			if(!empty($ptt)){
				$ptt = explode('-', $ptt);
					$tools[$key_last] = ['pt_id' => $ptt[0], 'when' => $ptt[1]];
			}else{
				unset($tools[$key_last]);
			}
		}

	}else{
		$tools = [];
	}

	return $tools;
}

//контроллер выполнения ДОП заданий в кроне. $when = 1 (перед заданием), $when = 2 (после задания)
public function cronToolsController($task, $when_do){
	
	//получаем из базы список всех заданий что нужно выполнить перед началом крона. Задания что еше не делались. 
	$toolse = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_cron_tools WHERE task_id=".(int)$task['id']." AND when_do =".(int)$when_do." AND scan = 0 ORDER BY id");

	//если задания есть то начинаем их выполнять.
	if($toolse->num_rows > 0){

		foreach ($toolse->rows as $tools) {

			//перед началом выполнения замеряем что бы у нас было 20 секунд.
			$this->cronChackTimeout(20);
			
			//получаем данные о патерне.
			$pattern = $this->toolGetPattern($tools['pt_id']);
			
			//отправляем задание на вполнение.
			$this->toolControlerFunction($pattern['setting'], $task['dn_id'], $who = 'cron');
			
			//помечаем записываем что задание было выполнено.
			$this->cronMarkTools($tools['id']);

		}
	}

}

//помеччаем задание как выполненое. 
public function cronMarkTools($cron_tools_id){
	//запрос на помечание задания как выполненое.
	$this->db->query("UPDATE ".DB_PREFIX."pars_cron_tools SET scan = 1 WHERE id=".$cron_tools_id);
}

############################################################################################
############################################################################################
#						Фунции отвечающие за поиск замену
############################################################################################
############################################################################################
//Получаем данные страницы поиск замена.
public function getReplacePage($dn_id,$param_id=''){
	$replace_links = $this->db->query("SELECT * FROM `". DB_PREFIX ."pars_link` WHERE dn_id=".(int)$dn_id." ORDER BY id ASC LIMIT 0, 3000");
	$connection = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_param WHERE id=".(int)$param_id);
	$connection = $connection->row;

	if(empty($connection)){
		$connection['id'] = 0;
		$connection['base_id'] = 0;
	}

	$replace = $this->db->query("SELECT r.id, r.dn_id, r.param_id, p.base_id, r.text_start, r.text_stop, r.rules, r.hash, r.arithm FROM ". DB_PREFIX ."pars_replace r INNER JOIN ". DB_PREFIX ."pars_param p ON r.param_id = p.id WHERE r.param_id=".(int)$param_id);
	$replace = $replace->row;

	$get_params = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_param WHERE dn_id=".(int)$dn_id." ORDER BY id ASC");
	$get_params = $get_params->rows;

	//помечаем в масиве выбранную гарницу. Нужно для оформления.
	foreach($get_params as $key => $params){
		$get_params[$key]['class'] = 'btn btn-default btn-sm btn-block';
		if($params['id'] == $connection['id']){
			$get_params[$key]['class'] = 'btn btn-success btn-sm btn-block';
		}
		if($params['id'] == $connection['base_id']){
			$get_params[$key]['class'] = 'btn btn-warning btn-sm btn-block';
		}
	}

	//если есть настройки поискз замена, привеодим их в формату вода в форму.
	if(!empty($replace['rules'])){
		//делаем из JSON массив php
		$rules = json_decode($replace['rules']);

		//Делаем из массива строку типа "Что_меняем|На_что_меняем"
		$replace['rules'] = '';
		foreach($rules as $var){
			if(isset($var[0]) && isset($var[1])){

				//проверяем если эта регулярка то никаких замен не делаем
				if(preg_match('#^\{reg\[(.*)\]\}$#', $var[0])){
					$str_rule = $var[0].'|'.$var[1];
				}else{
					$str_rule = str_replace('|','\|',$var[0]).'|'.$var[1];
				}

			}else{
				$str_rule = $var[0];
			}
			$replace['rules'] .= $str_rule.PHP_EOL;
		}
		//Убираем послдений ненужный перенос строки.
		$replace['rules'] = substr($replace['rules'], 0, -1);
	}
	
	//блок предп просмотра. Если есть.
	$data['show']['text_give'] = $this->getGranFromFile($param_id, 'input_text');
	$data['show']['text_get'] = $this->getGranFromFile($param_id, 'output');

	$data['params'] = $get_params;
	$data['replace'] = $replace;
	$data['replace_links'] = $replace_links->rows;
	#$this->wtfarrey($data);
	return $data;
}

//Сохраняем правила поиск замена
public function saveReplacePage($data,$dn_id,$param_id=''){

	if(empty($param_id)){
		$this->session->data['error'] = 'Не выбран параметр парсинга';
		return;
	}

	//разбираем входные данные для поиск замены.
	$data['rules'] = $this->parseRulesToReplace($data);

	//Если правила не пустые значет кодируем в json
	if(!empty($data['rules'])){
		$data['rules'] = json_encode($data['rules']);
	}

	$this->db->query("DELETE FROM `". DB_PREFIX ."pars_replace` WHERE param_id=".(int)$param_id);
	$res = $this->db->query("INSERT INTO ". DB_PREFIX ."pars_replace SET dn_id=".(int)$dn_id.", param_id=".(int)$param_id.", text_start='".$this->db->escape($data['text_start'])."', text_stop='".$this->db->escape($data['text_stop'])."', rules='".$this->db->escape($data['rules'])."', hash=".(int)$data['hash'].", arithm='".$this->db->escape($data['arithm'])."'");

	if($res){
		$this->session->data['success'] = 'Настройки сохранены успешно.';
	}
	#$this->wtfarrey($data);
}

public function parseRulesToReplace($data){

	if(!empty($data['rules'])){
		//Вот тут немного алгоритмов. Делим правила поиск замена на массив по принзнаку переноса строки.
		$data['rules'] = explode(PHP_EOL,$data['rules']);

		//Каждую строку делим еше на массив по принцепу ( массив из 2 элементов с разделителем с экранированием)
		foreach($data['rules'] as $key => $value){

			//отлавливаем регулярки в поиск замену
			if(preg_match('#^\{reg\[(.*)\]\}[|]#', $value, $temp_reg)){ #если регулярка
				//Удаляем из обшей строки правило регулярки
				$value = preg_replace('#^\{reg\[(.*)\]\}#','',$value);

				//правильно делим левую и правую сторону
				$parts = preg_split('#[|]#', $value,2);

				//Возвршаем в правую сторону правило регулярки
				$parts[0] = '{reg['.$temp_reg[1].']}';
				$parts[1] = str_replace(array("\r\n", "\r", "\n"), '', $parts[1]);
				$data['rules'][$key] = $parts;

			}else{#Если не регулярка

				$parts = preg_split('/(?<![^\\\\]\\\\)\|/', str_replace(array("\r\n", "\r", "\n"), '', $value), 2);
				array_walk($parts, function(&$v) { $v = str_replace('\\|', '|', $v); });
				$data['rules'][$key] = $parts;

			}

		}

	}else{
		$data['rules'] = '';
	}

	return $data['rules'];
}

//Пред просмотр поиск замена
public function showReplaceText($data, $param_id){
	
	//Получаем информацию о типе границы парсинга
	$param = $this->db->query("SELECT * FROM `". DB_PREFIX ."pars_param` WHERE id=".(int)$param_id);
	$param = $param->row;

	#Выбираем действие в зависимости от типа границы парсинга.
	if ($param['type'] == 2) {
		
		//Получаем сырой текст, или массив в повторяющихся границах.
		$text_give_t2 = $this->getGranFromFile($param_id, 'input_arr');
		if(empty($text_give_t2)){ $text_give_t2 = [];}

		$text_get = '';
		
		foreach ($text_give_t2 as $key => $value) {

			$value = $this->findReplace($value, $param_id);

			if($key == 0){ $text_get = $value; } else { $text_get = $text_get.$param['delim'].$value;}
		}

		//Проверка на то что бы был текст в пред просмотре и в массиве.
		if(!empty($data['text_give']) && empty($text_give_t2)){
			$data['text_give'] = '';
			$text_get = '';
		}

		$this->putGranToFile($text_get, $param_id, 'output');
		$this->putGranToFile($text_get, $param_id, 'output');

	} else {
		//проверяем есть ли текст для поиск замены.
		$text_get = $this->findReplace($data['text_give'], $param_id);
		$this->putGranToFile($text_get, $param_id, 'output');
		$this->putGranToFile($data['text_give'], $param_id, 'input_text');
	}

}

//Фунция парсинга для предпросмотра в поиск замене.
public function getParamShow($data, $param_id, $dn_id){

	//Сразу отработаем варианты отсуцтвия ссылок. И отсуцтвие выбранного параметра парсинга
	if(empty($data['download_link'])){
		$this->session->data['error'] = "Не выбрана ссылка для получения данных";
		return 1;
	}elseif(empty($param_id)){
		$this->session->data['error'] = "Не выбрана граница парсинга для получения данных";
		return 1;
	}

	//Получаем информацию о разделителе.
	$delim = $this->db->query("SELECT * FROM `". DB_PREFIX ."pars_param` WHERE id=".(int)$param_id);
	$delim = $delim->row;
	
	$data['download_link'] = str_replace('&amp;', '&', $data['download_link']);
	//Выполняем запрос на пред просмотр.
	$urls[] = $data['download_link'];
	$datas = $this->multiCurl($urls, $dn_id);
	//пишем логи, но не проверяем ошибку она не нужна в пред просмотре.
	$curl_error = $this->sentLogMultiCurl($datas[$data['download_link']], $dn_id);
	
	//Удаляем текст полс обработки от старой ссылки.
	$output_file = DIR_APPLICATION.'simplepars/replace/'.$param_id.'_output.txt';
	//Проверяем есть ли такой файл, и удаляем
	if (file_exists($output_file)) {	unlink($output_file); }

	//передаем код страницы
	$html = $datas[$data['download_link']]['content'];

	$text_give = $this->parsParam($html, $param_id);

	if(!empty($text_give)){

		if(is_array($text_give)){
			$text = '';

			foreach($text_give as $key => $value){
				$i = $key+1;
				#Выводит в поиск замену повторяющиеся границы парсинга. С разделителем.
				$text .='!========== Повторение [№'.$i.'] ========= Разделитель ['.$delim['delim'].'] ========== !'.PHP_EOL.PHP_EOL.$value.PHP_EOL.PHP_EOL;
			}

		}else{
			$text = $text_give;
		}

		$this->putGranToFile($text, $param_id, 'input_text');
		$this->putGranToFile($text_give, $param_id, 'input_arr');

	}else{#Есои при парсинге новой страницы параметр пустой. УДАЛЯЕМ
		$file_1 = DIR_APPLICATION.'simplepars/replace/'.$param_id.'_input_text.txt';
		$file_2 = DIR_APPLICATION.'simplepars/replace/'.$param_id.'_input_arr.txt';

		//Проверяем есть ли такой прайс.
		if (file_exists($file_1)) { unlink($file_1); }
		//Проверяем есть ли такой прайс.
		if (file_exists($file_2)) {	unlink($file_2); }

	}

}

//Фунция поиск замена
public function findReplace($value='', $param_id){

	//Поучаем значения поиск замена. Для этой границы
	$replace = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_replace WHERE param_id=".(int)$param_id);

	//преобразовываем входной текст.
	$value = html_entity_decode($value);

	#если есть правила делаем обработку.
	if($replace->num_rows){
		//переносим обьект в масив для удобства.
		$replace = $replace->row;

		//если правила поиск замена не пусты, выполняем поиск замену.
		if(!empty($replace['rules'])){
			//Взврашем json в массив
			$replace['rules'] = json_decode($replace['rules']);
			#$this->wtfarrey($replace['rules']);
			foreach($replace['rules'] as $rule){

				if(isset($rule[0]) && isset($rule[1])){

					$rule[0] = $this->pregRegLeft($rule[0]);
					$rule[1] = $this->pregRegRight($rule[1]);
					$value = preg_replace($rule[0], $rule[1], $value);
				}

			}
		}

		//Создаем уникальный артикул (хешируем полученные данные)
		if(!empty($replace['arithm'])){
			$value = $this->arithmNubers($value, $replace['arithm']);
		}

		if($replace['hash'] !=0 && !empty($value)){
			$value = substr(md5($value), 0, $replace['hash']);
		}

		//Добавляем значение в началао или конец строки.
		$value = htmlspecialchars_decode($replace['text_start']).$value.htmlspecialchars_decode($replace['text_stop']);
	}

	#$this->wtfarrey($value);
	return $value;
}

//Фунция составления герулярного выражения для левой части поиск замена
public function pregRegLeft($data){

	//Отлавливаем регулярные вырежения в правилах поиск замена
	if(preg_match('#^\{reg\[(.*)\]\}$#', $data, $reg)){

		$reg = htmlspecialchars_decode($reg[1]);

	}else{

		$reg = preg_quote(htmlspecialchars_decode($data), '#');
		//Что заменяем
		$what = ['\{skip\}','\{br\}'];
		//Чем заменяем
		$than = ['(.*?)','(\\r\\n|\\r|\\n)'];

		//Замена
		$reg = str_ireplace($what, $than, $reg);
		//Формируем полноценный патерн
		$reg = '#'.$reg.'#su';
		//Зашита от дурака.
		if($reg == '##su'){ $reg = '#^SimplePars$#su';}
	}
	#$this->wtfarrey($reg);
	return $reg;
}

//Фунция составления герулярного выражения для правой части поиск замена
public function pregRegRight($data){
	//Исчим если {br}

	//Модификатор добавления переноса строки
	if(strripos($data, '{br}')!==false){
		$data = str_replace('{br}', "\r\n", $data);
	}

	$data = html_entity_decode($data);
	return $data;
}

public function arithmNubers($value='', $arithm){
	//Преобразование данных их границы в число.
	$arithm = htmlspecialchars_decode($arithm);
	$value = (float)trim(str_replace(' ','',str_replace(',','.',$value)));
	$rounds = ['','']; #Временная переменная для очистки правила от алгоритма округления
	$step = 0.01;
	$site = '%';

	//разделяем на количество правил.
	$arithms = explode('&', $arithm);

	//Запускаем в цикле все правила к одной гарнице
	foreach ($arithms as $arithm) {
		//определяем условия окргуления.
		preg_match('#^\{(.*?)\}#', $arithm, $rounds);

		//Вырезаем алгоритм округления из общего правила, и приводим правило в нужный формат
		if(!empty($rounds[0])){

			//вырезаем кусок ненужный для наценки.
			$arithm = preg_replace('#^('.preg_quote($rounds[0]).'?)[\;]*#','',$arithm);

			//Приводим форматируем данные веденные пользователем. Да да, ведь вы все равно пишите лишние пробелы и запятые.
			$rounds[1] = trim(str_replace(' ','',str_replace(',','.',$rounds[1])));

			//Проверяем правильность ввода праила окргурления.
			if(preg_match('#^[0-9]+[,.]*[0-9]*[|]*[<>%]*$#',$rounds[1])){
				//делим правило на значение кратное которому округляем. И на условие округления
				$round = explode('|', $rounds[1]);
				$step = (float)$round[0];

				//Указываем условие округления
				if(!empty($round[1])){
					$site = $round[1];
				}
			}
		}

		$formula = explode(';', $arithm);
		//Запускаем калькуляцию.
		foreach($formula as $form){
			$form = trim(str_replace(',', '.', $form));
			//проверяем математическое правило
			#preg_match('#(^\([0-9]+[,.]?[0-9]*\-[0-9]+[,.]?[0-9]*\)[\-\+\/\*][0-9]+[,.]?[0-9]*)|(^[\-\+\/\*][0-9]+[,.]?[0-9]*)$#',$form)

			$break = false;
			//простой тип наценки
			if(preg_match('#^[\-\+\/\*][0-9]+[,.]?[0-9]*$#',$form)){

				//Действие
				$do = $form[0];
				$number = substr($form, 1);
				//Производим магию цифр
				switch ($do) {
					case '-':
						$value = $value - $number;
						$break = true;
						break;
					case '+':
						$value = $value + $number;
						$break = true;
						break;
					case '*':
						$value = $value * $number;
						$break = true;
						break;
					case '/':
						if($number != 0){ $value = $value / $number;	}
						$break = true;
						break;
				}

			}elseif(preg_match('#^[0-9]+[,.]?[0-9]*[\-\+\*\/][0-9]+[,.]?[0-9]*$#',$form)){

				$data = preg_split('#[\-\+\*\/]#', $form);
				$do = str_replace($data,'', $form);

				if($data[0] == $value && $do == '-'){
					$value = $value - $data[1];
					$break = true;
				}elseif($data[0] == $value && $do == '+'){
					$value = $value + $data[1];
					$break = true;
				}elseif($data[0] == $value && $do == '*'){
					$value = $value * $data[1];
					$break = true;
				}elseif($data[0] == $value && $do == '/' && $data[1] != 0){
					$value = $value / $data[1];
					$break = true;
				}

			//Сложный тип наценки
			}elseif(preg_match('#^\([0-9]+[,.]?[0-9]*\-[0-9]+[,.]?[0-9]*\)[\-\+\/\*][0-9]+[,.]?[0-9]*$#',$form)){

				//Получаем значение диапазона
				preg_match('#\((.*?)\)#', $form, $range_temp);
				$range = explode('-', $range_temp[1]);
				//Получаем действие, и number
				$form = preg_replace('#^\((.*?)\)#', '', $form);
				//Действие
				$do = $form[0];
				$number = substr($form, 1);

				//Производим магию цифр
				if($value >= $range[0] && $value <= $range[1] && $do == '-'){
					$value = $value - $number;
					$break = true;
				}elseif($value >= $range[0] && $value <= $range[1] && $do == '+'){
					$value = $value + $number;
					$break = true;
				}elseif($value >= $range[0] && $value <= $range[1] && $do == '*'){
					$value = $value * $number;
					$break = true;
				}elseif($value >= $range[0] && $value <= $range[1] && $do == '/' && $number != 0){
					$value = $value / $number;
					$break = true;
				}
			}

			//прерывание
			if($break){ break; }
		}

		//Округляем. По умолчанию до двух нулей после запятой.
		if($site == ">" && $step != 0){
			$value = ceil($value / $step) * $step;
		}elseif($site == '<' && $step != 0){
			$value = floor($value / $step) * $step;
		}else{
			if($step != 0){	$value = round($value / $step) * $step; }
		}

	}
	//Приводим число в приемлимый для csv формат
	$value = str_replace('.', ',', $value);
	#$this->wtfarrey($value);
	return $value;
}

//Фунция записи границы парсинга в файл, для пред просмотро.
public function putGranToFile($text, $param_id, $who){

	//Проверяем что бы была граница парсинга
	if($param_id > 0){
		//определяем место хранения файла границы парсинга.
		if($who == 'input_arr'){
			$file = DIR_APPLICATION.'simplepars/replace/'.$param_id.'_input_arr.txt';
			file_put_contents($file, json_encode($text));
		}elseif($who == 'output'){
			$file = DIR_APPLICATION.'simplepars/replace/'.$param_id.'_output.txt';
			file_put_contents($file, $text);
		}else{
			$file = DIR_APPLICATION.'simplepars/replace/'.$param_id.'_input_text.txt';
			file_put_contents($file, $text);
		}
	}
}

//Фунция чтения границ парсинга для поиск замены из файла.
public function getGranFromFile($param_id, $who){
	$data = '';
	//определяем место хранения файла границы парсинга.
	if($who == 'input_arr'){

		$file = DIR_APPLICATION.'simplepars/replace/'.$param_id.'_input_arr.txt';
		if (file_exists($file)) {
			$data = json_decode(file_get_contents($file), true);
		}

	}elseif($who == 'output'){

		$file = DIR_APPLICATION.'simplepars/replace/'.$param_id.'_output.txt';
		if (file_exists($file)) {
			$data = file_get_contents($file);
		}

	}elseif($who == 'input_text'){

		$file = DIR_APPLICATION.'simplepars/replace/'.$param_id.'_input_text.txt';
		if (file_exists($file)) {
			$data = file_get_contents($file);
		}
		
	}

	return $data;
}

############################################################################################
############################################################################################
#						Фунции связанные с Логами
############################################################################################
############################################################################################

public function saveLogSetting($data, $dn_id){
	#$this->wtfarrey($data);
	//Сохраняем настройки лога.
	if(empty($data['logs_reverse'])){ $data['logs_reverse'] = 0;}
	if(empty($data['logs_mb'])){ $data['logs_mb'] = 25;}

  $this->db->query("UPDATE `". DB_PREFIX ."pars_setting` SET
  	logs_reverse='".(int)$data['logs_reverse']."',
  	logs_mb=".(int)$data['logs_mb']."
  	WHERE `dn_id`=".(int)$dn_id);
}

//Создание лог файла
public function log($mark, $data, $dn_id){
	//Имя и адрес логов.
	$path = DIR_LOGS."simplepars_id-".$dn_id.".log";
	$text = date("Y-m-d H:i:s").'| ';

	//cURL отработал без ошибки
	if($mark == 'log_curl'){
		$text = PHP_EOL.date("Y-m-d H:i:s").'| ';
		$text .= 'Парсинг : ';

		//Определяем прокси
		if($data['browser']['proxy_use'] > 0){ $text_proxy = '| Прокси = ['.$data['browser']['proxy']['ip:port'].']';} else { $text_proxy = '';}

		if($data['errno'] == 0){

			if($data['http_code']==200){
	  		$text .='УСПЕШНЫЙ ЗАПРОС '.$text_proxy .' | Код ответа ['.$data['http_code'].'] Ссылка | '.$data['url'].PHP_EOL;
	  	}elseif($data['http_code']==404){
	  		$text .= 'ОШИБКА '.$text_proxy .' | Страница не найдена. Ответ сервера ['.$data['http_code'].'] Ссылка | '.htmlspecialchars_decode(trim($data['url'])).PHP_EOL;
	  	}elseif($data['http_code']==400){
	  		$text .= 'ОШИБКА '.$text_proxy .' | Неправильный запрос. Ответ сервера ['.$data['http_code'].'] Ссылка | '.htmlspecialchars_decode(trim($data['url'])).PHP_EOL;
	  	}elseif($data['http_code']==301){
	  		$text .= 'РЕДИРЕКТ '.$text_proxy .' | Ваш запрос перенаправлен. Ответ сервера ['.$data['http_code'].'] Ссылка входа - '.htmlspecialchars_decode(trim($data['url']))
	  		.' Адрес куда перенаправлен запрос | '.htmlspecialchars_decode(trim($data['redirect_url'])).PHP_EOL;
	  	}elseif($data['http_code']==302){
	  		$text .= 'РЕДИРЕКТ '.$text_proxy .' | Ваш запрос перенаправлен. Ответ сервера ['.$data['http_code'].'] Ссылка входа - '.htmlspecialchars_decode(trim($data['url']))
	  		.' Адрес куда перенаправлен запрос | '.htmlspecialchars_decode(trim($data['redirect_url'])).PHP_EOL;
	  	}elseif($data['http_code']==429){
	  		$text .= 'ОШИБКА '.$text_proxy .' | Страница недоступна, слишком много запросов за короткое время. Ответ сервера ['.$data['http_code'].'] Ссылка входа - '.htmlspecialchars_decode(trim($data['url']))
	  		.' Адрес куда перенаправлен запрос | '.htmlspecialchars_decode(trim($data['redirect_url'])).PHP_EOL;
	  	}else{
	  		$text .= 'НЕИЗВЕСТНЫЙ ОТВЕТ '.$text_proxy .' | Ответ сервера не распознан. Код ответа ['.$data['http_code'].'] Ссылка | '.htmlspecialchars_decode(trim($data['url'])).PHP_EOL;
	  	}

  	} elseif ($data['errno'] > 0){

  		$text .= 'ОШИБКА запроса '.$text_proxy .' | Код ошибки = '.$data['errno'].' | Текст ошибки = '.$data['errmsg'].' | Ссылка - '.htmlspecialchars_decode(trim($data['url'])).PHP_EOL;

  	}
  	#Записываем, или дозаписываем данные в лог фаил
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Запрос из кеша.
	if($mark == 'log_cache'){
		$text = PHP_EOL.date("Y-m-d H:i:s").'| ';
		$text .='=>[СТРАНИЦА ЗАГРУЖЕН ИЗ КЕША] Ссылка | '.$data['url'].PHP_EOL;
		#Записываем, или дозаписываем данные в лог фаил
		file_put_contents($path, $text, FILE_APPEND);
	}

	if($mark == 'cache_file_add'){
		$text = PHP_EOL.date("Y-m-d H:i:s").'| ';
		$text .='=>[СОЗДАН КЕШ] Ссылка | '.$data['url'].PHP_EOL;
		$text .= date("Y-m-d H:i:s").'| ->Файл кеша находится по адресу | '.$data['file'].PHP_EOL;
		#Записываем, или дозаписываем данные в лог фаил
		file_put_contents($path, $text, FILE_APPEND);
	}


	######################################### Работа с товаром ###################################
	//Добавления товара.
	if($mark == 'addProduct'){
		foreach ($data as $key => $value) {
			if($key == 0){
				$text .='->[ДОБАВЛЕНИЕ ТОВАР] ID = '.$value['pr_id'].' | Идентификатор '.$value['sid'].' = ['.$value['sid_value'].']'.PHP_EOL;
			}else{
				if(!empty($value['value']) || $value['value'] == 0){
					$text .= date("Y-m-d H:i:s").'| -->Данные | '.$value['name'].' = '.$value['value'].PHP_EOL;
				}
			}
		}
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Нету model а действие парсить модель.
	if($mark == 'NoParsModel'){
		$text .='!->[Товар не создан] : Вы выбрали действие {Парсить} в Код товара [model] Код не был найден на сайте доноре. Без кода невозможно создать товар. Рекомендуем поменять значение на {Создавать по умолчанию}. А код товара разместить в поле Артикул [sku]'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Парсинг не прошел проверку по определенным границам
	if($mark == 'NoGranPermit'){
		$text .='!->[Страница НЕ обработана ] : Поскольку -'.$data.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}
	//Нет идетификатора при добавлении товар
	if($mark == 'NoSid'){
		$text .='!->[Товар Не создан/Не обновлен] : Неспарсен идентификатора товара, '.$data['sid'].' | По ссылке '.$data['link'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Нет прошло все идентификаторы и не совпало ни с одним.
	if($mark == 'addProductNoSidCheck'){
		$text .='!->[Товар не создан] : Не один из идентификаторов не был обнаружен, возможно ошибка модуля сообщите разработчику модуля SimplePars. За ранние спасибо.'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Нет идетификатора при добавлении товар
	if($mark == 'addProductIsTrue'){
		$text .='!->[Товар не создан] : Товар с '.$data['sid'].'  = ['.$data['sid_value'].'] Уже существует в магазине и модуль его не создавал.'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Не найден товар для обновления.
	if($mark == 'NoFindProductToUpdate'){
		$text .='!->[Товар не обновлен] : В магазине не найден товар с '.$data['sid'].' = ['.$data['sid_value'].']'.' Ссылка | '.$data['link'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//URL
	if($mark == 'badUrl'){
		$text .='!->[SEO_URL не создан] : Отсутствуют данные в поле '.$data['name'].' для создания URL'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	if($mark == 'LogAddSeoUrl'){
		$text .='->[SEO_URL Создан] : '.$data['where'].' | SEO_URL= '.$data['url'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}



	////////////////////////////Обновление товара///////////////////////
	if($mark == 'UpdateProduct'){
		$datas = $data;
		foreach($datas as $key => $data){
			if($key == 0){
				$text .='->[ОБНОВЛЕН ТОВАР] ID = '.$data['pr_id'].' | Идентификатор '.$data['sid'].' = ['.$data['sid_value'].']'.PHP_EOL;
			}else{
				$text .= date("Y-m-d H:i:s").'| -->Обновление | '.$data['name'].' = '.$data['value'].PHP_EOL;
			}
		}
		file_put_contents($path, $text, FILE_APPEND);
	}

	//не обновился товар и не добавился.
	if($mark == 'NothingDoProduct'){
		$text .='!-->Действие добавлять и обновлять товар, товар не добавлен и не обновлен. Неизвестная ошибка.'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	################################### Сопутствующие логи при работе с товаром #######################
	//пришле запрос с парсинга фото.
	if($mark == 'curlImg'){
		$text .='->[ИЗОБРАЖЕНИЕ] : ';
		if($data['http_code']==200){
  		$text .='Загрузка успешна | Код ответа ['.$data['http_code'].'] Ссылка | '.$data['url'].PHP_EOL;
  	}elseif($data['http_code']==404){
  		$text .= 'Изображение НЕ НАЙДЕНО. Ответ сервера ['.$data['http_code'].'] Ссылка | '.htmlspecialchars_decode(trim($data['url'])).PHP_EOL;
  	}else{
  		if($data['errno']==6){
  			$text .= 'НЕ ЗАГРУЖЕНО Код ответа ['.$data['http_code'].'] Сообшение = ['.$data['errmsg'].'] Ссылка | '.htmlspecialchars_decode(trim($data['url'])).PHP_EOL;
  		}else{
  			$text .= 'НЕИЗВЕСТНЫЙ ОТВЕТ Ответ сервера не распознан. Код ответа ['.$data['http_code'].'] Ссылка | '.htmlspecialchars_decode(trim($data['url'])).PHP_EOL;
  		}
  	}
  	#Записываем, или дозаписываем данные в лог фаил
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Пришел запрос с добавления фото в товар.
	if($mark == 'fotoNotData'){
		$text .='->У товара нет фото ID = '.$data['pr_id'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Добавление категории.
	if($mark == 'addCat'){
		$text .='->КАТЕГОРИЯ СОЗДАНА : ID='.$data['id'].' Адрес категории = '.$data['cat_way'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	if($mark == 'LogAddNewOpt'){
		$text .='->ОПЦИЯ СОЗДАНА : ID='.$data['opt_id'].' Имя опции = '.$data['opt_name'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	if($mark == 'addOptToProduct'){
		$text .='->Добавлена опция в товар. | option_id='.$data['opt_id'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	if($mark == 'addNewOptionValue'){
		$text .='->Добавлено новое значение в опцию option_id ='.$data['opt_id'].' | Значение = ['.$data['value'].'] | value_id = '.$data['value_id'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

 	if($mark == 'doProductOptValueAdd'){
		$text .='->Добавлена опция в товаре option_id = '.$data['opt_id'].' | Добавлено значение опции value_id = '.$data['value_id'].' | Цена '.$data['pref'].' '.$data['price'].'| Количество = '.$data['quant'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

 	if($mark == 'doProductOptValueUp'){
		$text .='->Обновлена опция в товаре option_id = '.$data['opt_id'].' | Обновлено значение опции value_id = '.$data['value_id'].' | Цена '.$data['pref'].' '.$data['price'].'| Количество = '.$data['quant'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Добавление производителя.
	if($mark == 'addManuf'){
		$text .='->ПРОИЗВОДИТЕЛЬ СОЗДАН : ID='.$data['id'].' Название = '.$data['name'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Создан атрибут
	if($mark == 'AddNewAttr'){
		$text .='->СОЗДАН АТРИБУТ : Добавлен новый атрибут ['.$data['attr_name'].'] в группу id= '.$data['r_attr_group'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Создан атрибут
	if($mark == 'addAttrToProductLog'){
		$text .='->Добавлен атрибут в товар | attribute_id = '.$data['attr_id'].' | ['.$data['name'].'] = '.$data['value'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Добавление акции в товар.
	if($mark == 'addPriceSpecToProduct'){
		$text .='->Добавлена акционная цена = '.$data['price_spec'].' | Для групп(ы) покупателей = '.$data['group'].' | Сроком на = ['.$data['date'].']'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Не вышло создать атрибут
	if($mark == 'NoAddNewAttr'){
		$text .='->ОШИБКА : Модуль не смог создать новый атрибут с именем ['.$data['attr_name'].'] в группе id= '.$data['r_attr_group'].PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	/////////////////////////////////////////////////////
	//              логи от чекера прокси
	/////////////////////////////////////////////////////

	//Рабочий прокси
	if($mark == 'ProxyGood'){
		$text .='--> PROXY CHECKER | УСПЕХ | Прокси прошел проверку по вашим требованиям и добавлен в список проверенных | Прокси = [ '.$data['proxy'].' ]'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Ошибка при работе с прокси
	if($mark == 'ProxyError'){
		$text .='!-> PROXY CHECKER | ОШИБКА | Номер ошибки = '.$data['error'].' | Сообщение об ошибке = [ '.$data['error_msg'].' ] | Прокси = [ '.$data['proxy'].' ]'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Ошибка при работе с прокси
	if($mark == 'ProxyErrorHttp'){
		$text .='!-> PROXY CHECKER | ОШИБКА HTTP | Номер ошибки http = '.$data['http_code'].' | Прокси = [ '.$data['proxy'].' ]'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}

	//Нправильный формат записи прокси
	if($mark == 'ProxyBadFormId'){
		$text .='!-> PROXY CHECKER | ОШИБКА | Неправильный формат прокси | Прокси = [ '.$data['proxy'].' ]'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}
	//Подменяет сайт, отдает не те данные что ожидаются
	if($mark == 'ProxyChangeData'){
		$text .='!-> PROXY CHECKER | ОШИБКА | Подменяет данные сайта к которому вы обращаетесь | Прокси = [ '.$data['proxy'].' ]'.PHP_EOL;
		file_put_contents($path, $text, FILE_APPEND);
	}
}

//Получаем логи для вывода
public function getLogs($dn_id){
	$setting = $this->getSetting($dn_id);
	$log = '';
	$file = DIR_LOGS."simplepars_id-".$dn_id.".log";

	$size_mb = $setting['logs_mb']*1000000;

	//перенес с стандартной фунции
	if (file_exists($file)) {
		$size = filesize($file);

		if ($size >= ($size_mb) ) {

			$log = 'Извините логи не могут быть показаны поскольку размер файла simplepars_id-'.$dn_id.'.log превышает допустимые '.($size_mb/1000000).'мб'.PHP_EOL.'Но вы можете скачать лог файл себе на персональный компьютер и открыть его текстовым редактором.'.PHP_EOL.'После этого можете очистить логи что бы модуль начал писать заново.'.PHP_EOL.'Так же вы можете увеличить значение Размер выводимого лога.';
		} else {
			$log = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		}
	}
	return $log;
}
############################################################################################
############################################################################################
#						Фунции деление xml на части.
############################################################################################
############################################################################################
public function getSplitXmpPage($dn_id){
	//Получаем настройки поставшика
	$setting = $this->getSetting($dn_id);
	$data['setting'] = $setting;
	//получаем ссылки очереди.
	$links = $this->db->query("SELECT id, link FROM " . DB_PREFIX . "pars_sen_link WHERE `dn_id`=".(int)$dn_id." LIMIT 0,".$setting['page_cou_link']);
	$data['links'] = $links->rows;
	
	#$this->wtfarrey($data);
	return $data;
}

//получаем код страницы и выводим. 
public function xmlPieceCode($data, $dn_id){

	//сплошной текст что будет отдан.
	$show_code = '';
	//Получаем код страницы.
	$html = $this->CachePage($data['link'], $dn_id);
	//Указываем декодирование границ
	$start = htmlspecialchars_decode($data['param_start']);
	$stop = htmlspecialchars_decode($data['param_stop']);
	
	//зашита от дурака
	if(empty($start.$stop)){ $start ='rassol2granpars';  $stop='simpleparsgranpars';}
	//готовим правило
	$reg = '#'. preg_quote($start, '#').'(.*?)'.preg_quote($stop, '#') .'#su';

	preg_match_all($reg, $html, $pre_view);
	#$this->wtfarrey($pre_view);
	
	$i = 1;
	foreach($pre_view[0] as $text){
		$show_code .= '!=========================================================== Товар №'.$i.' ========================================================!'.PHP_EOL.PHP_EOL.$text.PHP_EOL.PHP_EOL;
				$i++;
		
		#$this->wtfarrey($piece);
	}
	#$this->wtfarrey($pre_view);
	return $show_code;
}
############################################################################################
############################################################################################
#						Фунции связанные с браузером
############################################################################################
############################################################################################

//Сохраняем настройки браузера
public function seveBrowser($data, $dn_id){
	#$this->wtfarrey($data);
	//Главная вкладка
	if(empty($data['proxy_use'])) { $data['proxy_use'] = 0;}
  if(empty($data['timeout'])) { $data['timeout'] = 15;}
  //if(empty($data['connect_timeout'])) { $data['connect_timeout'] = 10;}
  if(!isset($data['protocol_version'])) { $data['protocol_version'] = 2;}
  if(empty($data['header_get'])) { $data['header_get'] = 0;}
  if(!isset($data['followlocation'])) { $data['followlocation'] = 1;}
  if(empty($data['coockie_list'])) { $data['coockie_list'] = '';}
  if(empty($data['cookie_use'])) { $data['cookie_use'] = 0;}
  if(empty($data['cookie_session'])) { $data['cookie_session'] = 0;}
  if(empty($data['user_agent_list'])) { $data['user_agent_list'] = '';}
  if(!isset($data['user_agent_use'])) { $data['user_agent_use'] = 1;}
  if(empty($data['user_agent_change'])) { $data['user_agent_change'] = 0;}
  if(empty($data['header_list'])) { $data['header_list'] = '';} else { $data['header_list'] = $this->clearHeaders($data['header_list']);}
  if(empty($data['header_use'])) { $data['header_use'] = 0;}
  if(empty($data['header_change'])) { $data['header_change'] = 0;}
  if(!isset($data['cache_page'])) { $data['cache_page'] = 1;}

	//Вкладка чекера прокси
	if(empty($data['ch_connect_timeout'])) { $data['ch_connect_timeout'] = 5; }
	if(empty($data['ch_timeout'])) { $data['ch_timeout'] = 5; }
	if(empty($data['ch_url'])) { $data['ch_url'] = ''; }
	if(empty($data['ch_pattern'])) { $data['ch_pattern'] = ''; }

	$this->db->query("UPDATE `".DB_PREFIX."pars_browser` SET
		proxy_use = ".(int)$data['proxy_use'].",
		timeout = ".(int)$data['timeout'].",
		connect_timeout = ".(int)$data['timeout'].", 
		protocol_version = ".(int)$data['protocol_version'].",
		header_get = ".(int)$data['header_get'].",
		followlocation = ".(int)$data['followlocation'].",
		cookie_use = ".(int)$data['cookie_use'].",
		cookie_session = ".(int)$data['cookie_session'].",
		user_agent_use = ".(int)$data['user_agent_use'].",
		user_agent_change = ".(int)$data['user_agent_change'].",
		user_agent_list = '".$this->db->escape($data['user_agent_list'])."',
		header_use = ".(int)$data['header_use'].",
		header_change = ".(int)$data['header_change'].",
		header_list = '".$this->db->escape($data['header_list'])."',
		cache_page = ".(int)$data['cache_page'].",
		ch_connect_timeout = ".(int)$data['ch_connect_timeout'].",
		ch_timeout = ".(int)$data['ch_timeout'].",
		ch_url = '".$this->db->escape($data['ch_url'])."',
		ch_pattern = '".$this->db->escape($data['ch_pattern'])."'
		WHERE dn_id =".(int)$dn_id);


	//преобразовываем куки в формат Netscape и записываем в файл.
	$this->saveCookieJar($data['coockie_list'], $dn_id);

}

//Фунция преобразования данных в формат Netscape и запись в cookiejar
public function saveCookieJar($text, $dn_id){

	#$this->wtfarrey($text);
	$coockies = []; #массив по умолчанию.
	$rows = ''; #текст для записи в cookiejar
	//Путь к файлу
	$file = DIR_APPLICATION.'simplepars/cookie/cookie_'.$dn_id.'.txt';
	#$this->wtfarrey($text);
	//проверяем пусто или нет в переменной
	if(!empty($text)){

		$lines = explode(PHP_EOL, $text);

		//Проходим по массиву
		foreach ($lines as $key => $line) {

			//Основные параметры куков
			$name  = '';
			$value = '';
			$date  = 0;

			//проверяем что бы строка не была пустой.
			if((!empty($line[0]) && $line[0] != '#')) {

				$line = preg_split('#[;]#', $line, 2);

				#Проверяем полученный массив на принадлежность времени.
				if(!empty($line[1])){
					$data = explode('=', trim($line[1]));
					$time = strtotime(trim($line[0]));
				} else {
					$data = explode('=', trim($line[0]));
					$time = strtotime(0);
				}

				//проверяем на правильность заполнения
				if ( (!empty($data[0]) || $data[0] == 0) && isset($data[1]) ) {

					$rows .= ".simplepars.top\t"."TRUE\t"."/\t"."FALSE\t".$time."\t".$data[0]."\t".$data[1].PHP_EOL;

					#$coockies[$key][0] = '.simplepars.top'; #Доменное имя куки
					#$coockies[$key][1] = 'TRUE'; #Всем ли доступна эта кука ?
					#$coockies[$key][2] = '/'; #путь который может юзать куку
					#$coockies[$key][3] = 'FALSE'; # проверять безопасность соединение
					#$coockies[$key][4] = $time; #Время действия
					#$coockies[$key][5] = $data[0]; #Имя куки
					#$coockies[$key][6] = $data[1]; #Значение куки

				}

			}

		}


		//неважно есть он или нет мы его перезапишем.
		$handle = fopen($file, 'w+');
  	fclose($handle);

		//Еслимассив с куками не пустой тогда записываем его.
		if (!empty($rows)) {
			file_put_contents($file, $rows);
		}

	} else {
		#если пользователь отправил пустую форму с куками
		$handle = fopen($file, 'w+');
  	fclose($handle);
	}

	return $coockies;
}

//получение настроек браузера
public function getSettingBrowser($dn_id){
	$browser = [];
	$browser = $this->db->query("SELECT * FROM `".DB_PREFIX."pars_browser` WHERE `dn_id` =".(int)$dn_id);
	$browser = $browser->row;

	//Куки лист по умолчани
	$browser['cookie_list'] = '';
	//работа с куками
	$cookies = $this->readCookieJar($dn_id);
	#$this->wtfarrey($cookies);

	//если массив не пусито	 преобразовываем его.
	if (!empty($cookies)) {

		foreach($cookies as $key => $cookie){

			//проверяем правильность данных куки.
			if (isset($cookie[5]) && isset($cookie[6])) {
				//форматируем дату для удобства
				$date = date("Y-m-d H:i:s", (int)$cookie[4]) . ' ; ';

				#записываем все ф лист
				$browser['cookie_list'] .= $date.$cookie[5].'='.$cookie[6].PHP_EOL;
			}

		}

	}

	#$this->getBrowserToCurl($dn_id);
	return $browser;
}

//получение настроек для cURL
public function getBrowserToCurl($dn_id){
	$browser = [];
	$browser = $this->db->query("SELECT * FROM `".DB_PREFIX."pars_browser` WHERE `dn_id` =".(int)$dn_id);
	$browser = $browser->row;

	/////////////////////////
	//Работа с прокси
	/////////////////////////

	if($browser['proxy_use'] > 0){
		//выбираем тип прокси, 1 - весь список, 2 - только проверенный
		$browser['proxy'] = [];
		if($browser['proxy_use'] == 1){

			$proxy_list = $this->getProxyList($dn_id);
			$proxys['list'] = $proxy_list['list'];
			$proxys['max'] = $proxy_list['list_count'];

		} elseif ($browser['proxy_use'] == 2){

			$proxy_list = $this->getProxyList($dn_id);
			$proxys['list'] = $proxy_list['list_work'];
			$proxys['max'] = $proxy_list['list_work_count'];

		}

		//проверяем прокси лист пустой или нет.
		if(!empty($proxys['list'])){
			//Получаем рандомный проксик.
			$key_p = rand(0, $proxys['max']);
			$proxy_str = explode(':', $proxys['list'][$key_p]);

			//разбираем прокси на составляющие
			if(!empty($proxy_str[0]) && !empty($proxy_str[1])){

				$browser['proxy']['ip:port'] = $proxy_str[0].":".$proxy_str[1];
				$browser['proxy']['type'] = 0;
				$browser['proxy']['loginpass'] = '';

				//проверяем есть ли тип прокси
				if(!empty($proxy_str[2])) {
					$proxy_type = mb_strtoupper($proxy_str[2]);
					if($proxy_type == 'HTTP'){ 
						$proxy_type = CURLPROXY_HTTP; 
					}elseif($proxy_type == 'HTTPS'){ 
						$proxy_type = CURLPROXY_HTTP; 
					}elseif($proxy_type == 'SOCKS4'){ 
						$proxy_type = CURLPROXY_SOCKS4; 
					}elseif($proxy_type == 'SOCKS5'){ 
						$proxy_type = CURLPROXY_SOCKS5; 
					}
				}

				//Проверяем есть ли логин и пароль.
				if(!empty($proxy_str[3]) && !empty($proxy_str[4])){
					$browser['proxy']['loginpass'] = $proxy_str[3].':'.$proxy_str[4];
				}

			} else {
				#нету или ip или порта
				#такое прокси нельзя записать в модуль но все же оставлю место на обработку таких случаев.
				#Если понадобится
			}

		} else {
			#Если список прокси пустой, тогда не используем прокси
			$browser['proxy_use'] = 0;
		}

	}

	/////////////////////////
	//Работа с куками.
	/////////////////////////
	if($browser['cookie_use']){

		$browser['cookies'] = 'Cookie: ';
		//получаем массив кук c файла
		$cookies = $this->readCookieJar($dn_id);

		if (!empty($cookies)) {

			foreach ($cookies as $key => $cookie) {

				//прверяем использовать сесионные куки или нет. За одно и фильтрукм куки
				if ($browser['cookie_session']) {

					$browser['cookies'] .= $cookie[5]."=".$cookie[6]."; ";

				} else {

					if ($cookie[4] > 0) {

						$browser['cookies'] .= $cookie[5]."=".$cookie[6]."; ";

					}

				}

			}

		}

	} else {
		//Если выбранно неиспользовать куки.
		$browser['cookies'] = '';
	}

	///////////////////////////////
	//Работа с юсер агент.
	///////////////////////////////
	if($browser['user_agent_use']){

		$browser['user_agent_list'] = explode(PHP_EOL, $browser['user_agent_list']);

		//проверяем какой юсер агент использовать.
		if ($browser['user_agent_change']) {

			//если выбрано менять определяем диапазон и рандомно выбираем юсер агент.
			$max = count($browser['user_agent_list']) -1;
			//определяем рандомный ключ
			$key_u = rand(0, $max);
			//записываем юсер агент
			$browser['user_agent_list'] = "User-Agent: ".$browser['user_agent_list'][$key_u];

		} else {
			//Если не выбрано менять берем первый юсер агент из списка.
			$browser['user_agent_list'] = "User-Agent: ".$browser['user_agent_list'][0];
		}

	} else {
		$browser['user_agent_list'] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36";
	}

	///////////////////////////////
	//Работа с заголовками
	///////////////////////////////
	if ($browser['header_use']) {

		//разбиваем заголовки на массивы.
		$browser['header_list'] = preg_split("~^#(.*)~im", $browser['header_list']);

		if ($browser['header_change']) {

			//если выбрано менять определяем диапазон и рандомно выбираем.
			$max = count($browser['header_list']) -1;
			//определяем рандомный ключ
			$key_h = rand(0, $max);
			//записываем юсер агент
			$browser['header_list'] = explode(PHP_EOL, trim($browser['header_list'][$key_h]));

		} else {
			$browser['header_list'] = explode(PHP_EOL, trim($browser['header_list'][0]));
		}

	} else {
		$browser['header_list'] = [''];
	}

	//собираем правильные заголовки
	$browser['header_list'][] = $browser['cookies'];
	$browser['header_list'][] = $browser['user_agent_list'];
	$browser['header_list'] = array_filter($browser['header_list']);
	#$this->wtfarrey($browser);
	return $browser;
}

//Сохраняем прокси лист
public function saveProxyList($data, $dn_id){

	//Удаляем список прокси
	$this->db->query("DELETE FROM `".DB_PREFIX."pars_proxy_list` WHERE `dn_id`=".(int)$dn_id);

	if (!empty($data['proxy_list'])) {

		$proxy_list = explode(PHP_EOL, $data['proxy_list']);

		//составляем запрос на сохранение прокси листа.
		$sql_list = '';
		foreach ($proxy_list as $key => $list) {

			if ($key == 0){
				//проверяем на правильный формат ввода.
				if(strpos($list, ':') != false){
					$sql_list .= "('".$this->db->escape(trim($list))."', ".(int)$dn_id.", 0)";
				}

			} else {

				//проверяем на правильный формат ввода.
				if(strpos($list, ':') != false){
					$sql_list .= ",('".$this->db->escape(trim($list))."', ".(int)$dn_id.", 0)";
				}

			}

		}

		//проверяем что бы строка не была пустой.
		if (!empty($sql_list)) {
			$sql_list = "INSERT IGNORE INTO `".DB_PREFIX."pars_proxy_list`(`proxy`, `dn_id`, `status`) VALUES ".$sql_list;

			//записываем списко прокси
			$this->db->query($sql_list);
		}
	}
}

//Получаем список прокси ждя вывода на сайте
public function getProxyListToPage($dn_id){
	$list = ['list'=>'','list_work'=>'', 'list_count'=>0, 'list_work_count'=>0];
	$proxy_list = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_proxy_list WHERE dn_id=".(int)$dn_id." ORDER BY `id`");

	if($proxy_list->num_rows > 0){
		$wc = 0;
		foreach ($proxy_list->rows as $key => $value) {
			$list['list'] .= $value['proxy'].PHP_EOL;
			if($value['status'] == 1) { $list['list_work'] .= $value['proxy'].PHP_EOL; $wc++;}
		}
		$list['list_count'] = $key+1;
		$list['list_work_count'] = $wc;

	}

	#$this->wtfarrey($list);
	return $list;
}

//Получаем список прокси.
public function getProxyList($dn_id){
	$list = ['list'=>[],'list_work'=>[], 'list_count'=>0, 'list_work_count'=>0];
	$proxy_list = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_proxy_list WHERE dn_id=".(int)$dn_id);

	if($proxy_list->num_rows > 0){
		$wc = 0;
		foreach ($proxy_list->rows as $key => $value) {
			$list['list'][] = $value['proxy'];
			if($value['status'] == 1) { $list['list_work'][] = $value['proxy']; $wc++;}
		}
		$list['list_count'] = $key;
		$list['list_work_count'] = $wc-1;

	}

	#$this->wtfarrey($list);
	return $list;
}

//проверка прокси
public function startCheckProxy($dn_id){
	//Получаем настройки
	$browser = $this->getSettingBrowser($dn_id);

	//Получаем данные расчета прогрес бара.
	$proxy_ip = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_proxy_list WHERE dn_id=".(int)$dn_id." AND `status` = 0 ORDER BY id");
	$lists = $proxy_ip;
	$totals = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_proxy_list WHERE dn_id=".(int)$dn_id);
	$progress = ($totals->num_rows-$lists->num_rows)/($totals->num_rows/100);

	//проверяем есть ли ссылка, и шаблон для проверки.
	if(empty($browser['ch_url'])){
		$answ['progress'] = 100;
		$answ['proxy_done'] = $totals->num_rows-$lists->num_rows;
		$answ['proxy_wait'] = $lists->num_rows;
		$this->answjs('finish', 'Проверка прокси остановлена. Укажите ссылку на сайт донор.', $answ);
	}

	if(empty($browser['ch_pattern'])){
		$answ['progress'] = 100;
		$answ['proxy_done'] = $totals->num_rows-$lists->num_rows;
		$answ['proxy_wait'] = $lists->num_rows;
		$this->answjs('finish', 'Проверка прокси остановлена. Укажите проверочный текст', $answ);
	}

	//Основной блок работы.
	if ($proxy_ip->num_rows > 0) {
		$proxy_ip = $proxy_ip->row;

		$proxy = explode(':', $proxy_ip['proxy']);

		//проверяем что бы у прокси было и ip и порт
		if (!empty($proxy[0]) && !empty($proxy[1])) {
			//создаем ип и прокси для проверки.
			$ip_port = $proxy[0].':'.$proxy[1];
			$loginpass = '';
			$proxy_type = CURLPROXY_HTTP;

			//проверяем указан ли тип прокси
			if(!empty($proxy[2])){
				#$this->wtfarrey($proxy_type);

				$proxy_type = mb_strtoupper($proxy[2]);
				#$this->wtfarrey($proxy_type);

				if($proxy_type == 'HTTP'){ 
					$proxy_type = CURLPROXY_HTTP; 
				}elseif($proxy_type == 'HTTPS'){ 
					$proxy_type = CURLPROXY_HTTP; 
				}elseif($proxy_type == 'SOCKS4'){ 
					$proxy_type = CURLPROXY_SOCKS4; 
				}elseif($proxy_type == 'SOCKS5'){ 
					$proxy_type = CURLPROXY_SOCKS5; 
				}
			}
			#$this->wtfarrey($proxy_type);
			//если в прокси есть еше логин и пароль, тогда и его применяем.
			if (!empty($proxy[3]) && !empty($proxy[4])) {
				$loginpass = $proxy[3].':'.$proxy[4];
			}

			/////////////////////////////////
			# выполняем проверочный запрос
			/////////////////////////////////
			$uagent = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36";
		  $ch = curl_init($browser['ch_url']);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
		  curl_setopt($ch, CURLOPT_HEADER, 0);           // возвращает заголовки
		  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
		  curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки | Проблемы в понимании этой опции. Отключил
		  curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
		  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $browser['ch_connect_timeout']); // таймаут соединения
		  curl_setopt($ch, CURLOPT_TIMEOUT, $browser['ch_timeout']);        // таймаут ответа
		  curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
		  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //Отключить проверку сертификата.
		  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //проверяет принадлежность сертификата к сайту.

		  //настройки прокси
		  #$ip_port = '51.79.141.24:8080';
		  curl_setopt($ch, CURLOPT_PROXY, $ip_port);
		  #$this->wtfarrey($ip_port);
		  curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy_type);
			#$this->wtfarrey($proxy_type);

		  #Если указан логин и пароль прокси
		  if(!empty($loginpass)){
		  	curl_setopt($ch, CURLOPT_PROXYUSERPWD, $loginpass);
			}
			#$this->wtfarrey($loginpass);
		  $content = curl_exec( $ch );
		  $err     = curl_errno( $ch );
		  $errmsg  = curl_error( $ch );
		  $data  = curl_getinfo( $ch );
		  curl_close( $ch );

		  $data['errno']   = $err;
		  $data['errmsg']  = $errmsg;
		  $data['content'] = $content;
		  //приводит страницу к единой кодировке.
		  $data = $this->findCharsetSite($data, $dn_id);

		  //Если выскачила ошибка сообщаем ее
		  if($data['errno'] > 0) {
		  	$this->db->query("UPDATE ".DB_PREFIX."pars_proxy_list SET `status` = 2 WHERE `id` = ".(int)$proxy_ip['id']);

		  	$logs = ['proxy'=>$proxy_ip['proxy'], 'error'=>$data['errno'], 'error_msg'=>$data['errmsg']];
				$this->log('ProxyError',$logs, $dn_id);

		  	$answ['progress'] = $progress;
		  	$answ['proxy_done'] = $totals->num_rows-$lists->num_rows;
				$answ['proxy_wait'] = $lists->num_rows;
				$this->answjs('go', $data['errmsg'], $answ);

		  } elseif($data['http_code'] > 399){

				$this->db->query("UPDATE ".DB_PREFIX."pars_proxy_list SET `status` = 2 WHERE `id` = ".(int)$proxy_ip['id']);

		  	$logs = ['proxy'=>$proxy_ip['proxy'], 'http_code'=>$data['http_code']];
				$this->log('ProxyErrorHttp',$logs, $dn_id);

		  	$answ['progress'] = $progress;
		  	$answ['proxy_done'] = $totals->num_rows-$lists->num_rows;
				$answ['proxy_wait'] = $lists->num_rows;
				$this->answjs('go', $data['errmsg'], $answ);

		  }else{

		  	//Ишим указанный текст на странице
		  	$pattern = htmlspecialchars_decode($browser['ch_pattern']);

		  	if (preg_match('#'.preg_quote($pattern, '#').'#su', $data['content'])){

		  		$this->db->query("UPDATE ".DB_PREFIX."pars_proxy_list SET `status` = 1 WHERE `id` = ".(int)$proxy_ip['id']);

			  	$logs = ['proxy'=>$proxy_ip['proxy']];
					$this->log('ProxyGood',$logs, $dn_id);

			  	$answ['progress'] = $progress;
			  	$answ['proxy_done'] = $totals->num_rows-$lists->num_rows;
					$answ['proxy_wait'] = $lists->num_rows;
					$this->answjs('go', 'Прокси прошел проверку по вашим требования', $answ);

		  	} else {

		  		$this->db->query("UPDATE ".DB_PREFIX."pars_proxy_list SET `status` = 2 WHERE `id` = ".(int)$proxy_ip['id']);

		  		$logs = ['proxy'=>$proxy_ip['proxy']];
					$this->log('ProxyChangeData',$logs, $dn_id);

		  		$answ['progress'] = $progress;
		  		$answ['proxy_done'] = $totals->num_rows-$lists->num_rows;
					$answ['proxy_wait'] = $lists->num_rows;
					$this->answjs('go', 'Подменяет данные сайта к которому вы обращаетесь. Проверочный текст не найден', $answ);
		  	}

		  }

		} else {
			//Кривой прокси поменчаем его как мертвый.
			$this->db->query("UPDATE ".DB_PREFIX."pars_proxy_list SET `status` = 2 WHERE `id` = ".(int)$proxy_ip['id']);

			$logs = ['proxy'=>$proxy_ip['proxy']];
			$this->log('ProxyBadFormId',$logs, $dn_id);

			$answ['progress'] = $progress;
			$answ['proxy_done'] = $totals->num_rows-$lists->num_rows;
			$answ['proxy_wait'] = $lists->num_rows;
			$this->answjs('go', 'Неправильный формат прокси', $answ);
		}

	} else {
		$answ['progress'] = 100;
		$answ['proxy_done'] = $totals->num_rows-$lists->num_rows;
		$answ['proxy_wait'] = $lists->num_rows;
		$this->answjs('finish', 'Проверка прокси закончена', $answ);
	}

	#$this->wtfarrey($browser);
}

//очистить список прокси
public function clearProxyList($dn_id){

	$this->db->query("DELETE FROM `".DB_PREFIX."pars_proxy_list` WHERE `dn_id`=".(int)$dn_id);

}

//сбросить список проверенных прокси
public function resetProxyList($dn_id){

	$this->db->query("UPDATE `".DB_PREFIX."pars_proxy_list` SET `status` = 0 WHERE `dn_id` =".(int)$dn_id);

}

//Функция чтения файлов куки
public function readCookieJar($dn_id){

	//Возврашаемый массив
	$cookies = [];
	//Адресс файла
	$file = DIR_APPLICATION.'simplepars/cookie/cookie_'.$dn_id.'.txt';
	if(file_exists($file)){

		//разбираем Netscape формат куков
		$file = file_get_contents($file);

		//очишаем куки от бага.
		$file = str_replace('#HttpOnly_', '.', $file);

		//делим строки на массив
		$lines = explode(PHP_EOL, $file);

		//перебераем каждую строку и работаем с ней.
		foreach($lines as $line) {

			//Берем только непустые строки, и строки которые не являются комнтарием
			if((!empty($line[0]) && $line[0] != '#') && substr_count($line, "\t") == 6) {

				//получаем конкретную куку. и работаем только с ней.
				$cookie = explode("\t", $line);
				//очишаем от ненужных пробелов
				$cookie = array_map('trim', $cookie);

				#$this->wtfarrey($cookie);

				//передаем куки в массив
				$cookies[] = $cookie;
			}

		}

	}

	return $cookies;
}

//очистка заголовков от ненужных данных. Зашита от дурака.
public function clearHeaders($text){
	#Очишаем при сохранении заголовки от ненужных данных.
	$text = explode(PHP_EOL, $text);

	foreach ($text as $key => $value) {

	  if (empty($value)) {
	    unset($text[$key]);
	  }elseif (preg_match('#^Host:(.*)#im', $value)) {
	    unset($text[$key]);
	  }elseif (preg_match('#^User-Agent:(.*)#im', $value)) {
	    unset($text[$key]);
	  }elseif (preg_match('#^Cookie:.*#im', $value)) {
	    #unset($text[$key]);
	  }

	}

	$text = implode(PHP_EOL, $text);
	return $text;
}
############################################################################################
############################################################################################
#						Фунции отвечающие за парсинг. Парсин страницы, разбор данных все здесь
############################################################################################
############################################################################################

//Фунция мульти курл отправление запросов
public function multiCurl($urls, $dn_id, $browser=[]){
  
  //получаем настройки браузера, ели они не пришли из вне.
  if(empty($browser)){
  	$browser = $this->getBrowserToCurl($dn_id);
	}

  $datac = [];
  $datas = [];

  //Проверяем нужно ли использовать кеш.
  if($browser['cache_page'] == 1){

  	//проверяем есть ли кеш ссылки, если есть удаляем ее из массива на парсинг.
  	foreach ($urls as $key => $link) {
  		$cache = $this->getCachePageFromFile($link, $browser, $dn_id);
  		if($cache){ 
  			$datac[$link] = $cache;
  			unset($urls[$key]);
  		}
  	}

  }

  //проверяем пустой ли массив или нет. Если не пустой отправляем его на выполнение курлом. 
  if($urls){
    $datas = $this->curlRequest($urls, $browser, $dn_id);
  }
  //обьеденяем кеш, и новые ссылки.
  foreach($datac as $key => $value){
  	$datas[$key] = $value;
  }

  return $datas;
}

public function curlRequest($urls, $browser, $dn_id){
	//Файлы куки
  $cookiefile = DIR_APPLICATION.'simplepars/cookie/cookie_'.$dn_id.'.txt';
	
	//Формируем массив для ответа.
  $datas = array();
	#$this->wtfarrey($browser);
	//Создаем дескотпьлр запроса
  $mh = curl_multi_init();
  $curl_array = array();
  foreach($urls as $i => $url) {
  	$url = $this->urlEncoding($url);
  	#$this->wtfarrey($url);
   	$curl_array[$i] = curl_init($url);
	  //настраиваем браузер
	  curl_setopt($curl_array[$i], CURLOPT_HTTP_VERSION, $browser['protocol_version']);
	  curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, 1);                            // возвращает веб-страницу
	  curl_setopt($curl_array[$i], CURLOPT_HEADER, $browser['header_get']);               // возвращает заголовки
	  curl_setopt($curl_array[$i], CURLOPT_FOLLOWLOCATION, $browser['followlocation']);   // переходит по редиректам
	  curl_setopt($curl_array[$i], CURLOPT_MAXREDIRS, 100);                                // останавливаться после 10-ого редиректа
	  curl_setopt($curl_array[$i], CURLOPT_ENCODING, "");                                 // обрабатывает все кодировки 
	  #curl_setopt($curl_array[$i], CURLOPT_USERAGENT, $browser['user_agent_list']);      // useragent ПЕРЕДАЮ ЧЕРЕЗ ЗАГОЛОВКИ
	  curl_setopt($curl_array[$i], CURLOPT_TIMEOUT, $browser['timeout']);     // Максимально позволенное количество секунд для выполнения cURL-функций.
	  curl_setopt($curl_array[$i], CURLOPT_CONNECTTIMEOUT, $browser['timeout']);  // таймаут соединения
	  curl_setopt($curl_array[$i], CURLOPT_SSL_VERIFYPEER, FALSE);                        // Отключить проверку сертификата.
	  curl_setopt($curl_array[$i], CURLOPT_SSL_VERIFYHOST, FALSE);                        // проверяет принадлежность сертификата к сайту.
	  curl_setopt($curl_array[$i], CURLOPT_HTTPHEADER, $browser['header_list']);
	  if($browser['cookie_use']){
	  	curl_setopt($curl_array[$i], CURLOPT_COOKIEJAR, $cookiefile);                     // Подключаем работу кук
	  }

	  //Использование прокси
	  if($browser['proxy_use']){
	  	curl_setopt($curl_array[$i], CURLOPT_PROXY, $browser['proxy']['ip:port']);				// Указываем прокси ип и порт
			curl_setopt($curl_array[$i], CURLOPT_PROXYTYPE, $browser['proxy']['type']);				// Тип прокси
			#Если указан логин и пароль прокси
			if(!empty($browser['proxy']['loginpass'])){														// логин пароль от прокси
			 	curl_setopt($curl_array[$i], CURLOPT_PROXYUSERPWD, $browser['proxy']['loginpass']);
			}
	  }

	  curl_multi_add_handle($mh, $curl_array[$i]);
  }

  //Волшебные строки мульти запроса.
  $running = NULL;
  do {
    usleep(10000);
    curl_multi_exec($mh,$running);
  } while($running > 0);

  //Формируем ответ.
  foreach($urls as $i => $url) {
    $datas[$url] = curl_getinfo($curl_array[$i]);
    $erno = curl_multi_info_read($mh);
    $datas[$url]['url'] = $url;
    $datas[$url]['errno'] = $erno['result'];
    $datas[$url]['errmsg'] = curl_error($curl_array[$i]);
    $datas[$url]['sp_log'] = 'log_curl';
    $datas[$url]['browser'] = $browser;
    $datas[$url]['content'] = curl_multi_getcontent($curl_array[$i]);
		#$this->wtfarrey($datas);

    //Если сайт вернул не пустую страницу добавляем туд ссылку на сайт.
    if(!empty($datas[$url]['content'])){ $datas[$url]['content'] = '#[url]'.$url.'[/url]'.PHP_EOL.$datas[$url]['content'];}
  }
      
  foreach($urls as $i => $url){
    curl_multi_remove_handle($mh, $curl_array[$i]);
  }
  curl_multi_close($mh);

  //по очереди обрабатываем каждый ответ из мулти запроса.
  foreach ($datas as $key => $data) {
   	$data = $this->findCharsetSite($data, $dn_id);
	 	$datas[$key] = $data;
  }


  //проверяем нужно ли работать с кешом. И при необходимости записываем его в файл.
  if( ($browser['cache_page'] == 1) || ($browser['cache_page'] == 2) ){
	  foreach($datas as $key => $data){
	  	//делаем проверку что бы не кешировать страницы с ошибками.
	  	if($data['errno'] == 0) {
	   		$this->putCachePageOnFile($data, $key, $dn_id);
	   	}
	  }
	}
	#$this->wtfarrey($datas);
  return $datas;
}

//Фунция скачивания img
public function curlImg($urls, $dn_id){

	//Формируем массив для ответа.
  $datas = array();
	
	//Создаем дескотпьлр запроса
  $mh = curl_multi_init();
  $curl_array = array();
  $uagent = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36";
  foreach($urls as $i => $url) {
   	$curl_array[$i] = curl_init($url);
	  //настраиваем браузер
	  curl_setopt($curl_array[$i], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	  curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, 1);                            // возвращает веб-страницу
	  curl_setopt($curl_array[$i], CURLOPT_HEADER, 0);               											// возвращает заголовки
	  curl_setopt($curl_array[$i], CURLOPT_FOLLOWLOCATION, 1);   													// переходит по редиректам
	  curl_setopt($curl_array[$i], CURLOPT_MAXREDIRS, 10);                                // останавливаться после 10-ого редиректа
	  curl_setopt($curl_array[$i], CURLOPT_ENCODING, "");                                 // обрабатывает все кодировки 
	  curl_setopt($curl_array[$i], CURLOPT_USERAGENT, $uagent);      											// useragent ПЕРЕДАЮ ЧЕРЕЗ ЗАГОЛОВКИ
	  curl_setopt($curl_array[$i], CURLOPT_TIMEOUT, 20);     															// Максимально позволенное количество секунд для выполнения cURL-функций.
	  curl_setopt($curl_array[$i], CURLOPT_CONNECTTIMEOUT, 20);  													// таймаут соединения
	  curl_setopt($curl_array[$i], CURLOPT_SSL_VERIFYPEER, FALSE);                        // Отключить проверку сертификата.
	  curl_setopt($curl_array[$i], CURLOPT_SSL_VERIFYHOST, FALSE);                        // проверяет принадлежность сертификата к сайту.

	  curl_multi_add_handle($mh, $curl_array[$i]);
  }

  //Волшебные строки мульти запроса.
  $running = NULL;
  do {
    usleep(10000);
    curl_multi_exec($mh,$running);
  } while($running > 0);

  //Формируем ответ.
  foreach($urls as $i => $url) {
    $datas[$i] = curl_getinfo($curl_array[$i]);
    #$erno = curl_multi_info_read($mh);
    $datas[$i]['content'] = curl_multi_getcontent($curl_array[$i]);
  }
  
  //Формируем ответ.
  foreach($urls as $i => $url) {
    $datas[$i] = curl_getinfo($curl_array[$i]);
    $erno = curl_multi_info_read($mh);
    $datas[$i]['url'] = $url;
    $datas[$i]['errno'] = $erno['result'];
    $datas[$i]['errmsg'] = curl_error($curl_array[$i]);
    $datas[$i]['content'] = curl_multi_getcontent($curl_array[$i]);

  }

  foreach($urls as $i => $url){
    curl_multi_remove_handle($mh, $curl_array[$i]);
  }
  curl_multi_close($mh);


  $imgs = [];
  //по очереди обрабатываем каждый ответ из мулти запроса.
  foreach ($datas as $key => $data) {

   	$imgs[] = [
   							'url'=> $data['url'], 
   							'img' => $data['content']
   						];

   	//пишем логи.
   	$this->log('curlImg', $data, $dn_id);
  }
  unset($datas);
	#$this->wtfarrey($imgs);
  return $imgs;
}

//Данная фунция морально устарела. Когду бедут время прокачай ее.
public function CachePage($url, $dn_id){
	$url = str_replace('&amp;', '&', $url);
	//Выполняем запрос
	$urls[] = $url;
	$datas = $this->multiCurl($urls, $dn_id);
	//пишем логи, но не проверяем ошибку она не нужна в пред просмотре.
	$curl_error = $this->sentLogMultiCurl($datas[$url], $dn_id);
	if($curl_error){
		$datas[$url]['content'] .= "НЕУДАЧНЫЙ ЗАПРОС!!!".PHP_EOL;
  	$datas[$url]['content'] .= "Номер ошибки = ".$datas[$url]['errno'].PHP_EOL;
  	$datas[$url]['content'] .= "Текст ошибки = ".$datas[$url]['errmsg'].PHP_EOL;
  	$datas[$url]['content'] .= "Ссылка = ".$datas[$url]['url'].PHP_EOL;
  	$datas[$url]['content'] .= "Больше информации можно получить в логах модуля SimplePars";
	}

	return $datas[$url]['content'];
}

//Универсальная фунция парсинга одного параметра. Входные данные id параметра. HTML для парсинга.
//Поиск замена вызывается тоже здесь, и обработка регулярок тоже. На выходе чистая выжимка данных!
public function parsParam($html, $param_id){
	$param = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_param WHERE id=".(int)$param_id);
	$param = $param->row;

	if($param['type'] == 1){

		//Обычные границы парсинга
	  $start = htmlspecialchars_decode($param['start']);
		$stop = htmlspecialchars_decode($param['stop']);

		$reg = '#'. preg_quote($start, '#').'(.*?)'.preg_quote($stop, '#') .'#su';
		preg_match_all($reg, $html, $value);


		//Выбираем нужные элемент, по настройкам пропуска
		$value[$param['with_teg']] = $this->skipEntryParam($value[$param['with_teg']], 1, $param['skip_where'], $param['skip_enter']);

		//Проверяем на присуцтвие.
		if(empty($value[$param['with_teg']])){ $value[$param['with_teg']][0]=''; }

		$pars_data = $value[$param['with_teg']][0];

	}elseif($param['type'] == 2){

		//повторяющиеся границы
		//Получаем данные базовой границы
		if($param['base_id'] != 0){
			$param_base = $this->db->query("SELECT * FROM ". DB_PREFIX ."pars_param WHERE id=".(int)$param['base_id']);
			$param_base = $param_base->row;

			$start_base = htmlspecialchars_decode($param_base['start']);
	   	$stop_base = htmlspecialchars_decode($param_base['stop']);

	   	$reg = '#'. preg_quote($start_base, '#').'(.*?)'.preg_quote($stop_base, '#') .'#su';

	   	preg_match_all($reg, $html, $code);

			if(empty($code[$param_base['with_teg']][0])){$code[$param_base['with_teg']][0]=' ';}

	   	$code[$param_base['with_teg']][0] = $this->findReplace($code[$param_base['with_teg']][0], $param['base_id']);

			// определяем порядок вхождения
			$code[$param_base['with_teg']] = $this->skipEntryParam($code[$param_base['with_teg']], 1, $param_base['skip_where'], $param_base['skip_enter']);
			#$this->wtfarrey($code[$param_base['with_teg']]);
			//Если пустой массив тогда делаем в нем пробел. Непомню почему я так решил, но знаю что нужно так.
			if(empty($code[$param_base['with_teg']][0])){ $code[$param_base['with_teg']][0]=''; }

		}else{
				$code[0][0] = $html;
				//Если используется вся страница
				$param_base['with_teg'] = 0;
		}

  	//А теерь повторяющие границы парсинга
	 	$start = htmlspecialchars_decode($param['start']);
	  $stop = htmlspecialchars_decode($param['stop']);
	  
	  //!--Во фикс проблемы с пустой границей. Можено удалить в случаи чего. Не критичный косяк
	  if(empty($start)){ $start = 'simpleparsrassol2';}
	  if(empty($stop)){ $stop = 'simpleparsrassol2';}
	  //--!конец фикса.

	 	$reg = '#'. preg_quote($start, '#').'(.*?)'.preg_quote($stop, '#') .'#su';

	 	preg_match_all($reg, $code[$param_base['with_teg']][0], $values);

	 	#$pars_data = $values[$param['with_teg']];

	 	$pars_data = $this->skipEntryParam($values[$param['with_teg']], 2, $param['skip_where'], $param['skip_enter']);

	 	//Используем реверс если он задан
	 	if($param['reverse'] == 1){
	 		$pars_data = array_reverse($pars_data);
	 	}

	}

	#$this->wtfarrey($pars_data);
	return $pars_data;
}

//Подсчет пропуска вхождений. Выбираем какую итерацию пропустить.
public function skipEntryParam($data, $type, $skip_where, $num=0){

	//проверяем данные
	if(!empty($data) && !empty($type) && $num !== 0){

		//Поскольку к каждой границе свой подход определяем тип
		if($type ==1){

			$num = (int)$num;

			if($skip_where==2){
				$num = -($num + 1);
			}

			$data = array_slice($data, $num, 1);


		}elseif($type ==2){

			$num = explode('-', $num);
			#Приводим в порядок диапазоны
			if(empty((int)$num[0])){ $num[0] = 0 ;}

			if(empty($num[1])){
			 $num[1] = null;
			}else{
				$num[1] = (int)$num[1];
			}

			//Если пользователь что то криво написал выходим из общета отдаем как есть.
			if($num[0] == 0 && $num[1] == 0){
				return $data;
			}

			#производим определяем сторону отсчета.
			if($skip_where == 2){

				if($num[1] === null){
					$num[1] = -($num[0]);
					$num[0] = 0;
				}else{
					$num[0] = -($num[0] + $num[1]);
				}
				//производим преобразования массива, откидываем ненужные значения.
				$data = array_slice($data, $num[0], $num[1]);

			}elseif($skip_where == 3){

				$data = array_slice($data, $num[0], null);

				if($num[1] != 0){
					$data = array_slice($data, null, -$num[1]);
				}

			}else{
				//производим преобразования массива, откидываем ненужные значения.
				$data = array_slice($data, $num[0], $num[1]);
			}
			#$this->wtfarrey($num);
		}

	}


	return $data;
}

//Пределяем кодировку сайта.
public function findCharsetSite($data, $dn_id){
	$bad_car = ['UTF8'];
	#$content = $data['content'];
  #$this->wtfarrey($data);

  #Получаем иходную кодировку из заголовка.
  preg_match('#charset\=(.*?)$#Ui', $data['content_type'], $chrs);

  if(!empty($chrs[1])){
    $charset = $chrs[1];
  }else{
    $charset = '';
  }
	$charset = str_replace($bad_car, 'UTF-8', $charset);
  //Еше один костыль который нужно будет исправлять в будушем.
  if(empty($charset)){

      #$reg = '#charset=(.*?)"#U';
      $reg = '#charset=(.*?)"#Ui';
			preg_match($reg, $data['content'], $chrs);

			if(!empty($chrs[1])){
				$charset = $chrs[1];
			}else{
				$charset = 'UTF-8';
			}
  }

  #$this->wtfarrey($charset);
  //Вычишаем всякий шлак, по мере обнаружения буду увеличивать правила.
  $charset = str_ireplace('"', '', $charset);

  //Перекодируем страницу в UTF-8
  $data['content'] = @mb_convert_encoding($data['content'], "UTF-8", $charset);
  
  return $data;
}

//запись лога мульти курла.
public function sentLogMultiCurl($data, $dn_id){
	//маячек об ошибке.
	$value['error'] = 0;
	$value['http_code'] = '';

	//по очереди обрабатываем каждый ответ из мулти запроса.
	if($data['errno'] > 0) {
		//если ошибка
		$value['error'] = 1;
		$value['http_code'] = $data['errno'];
  	$this->log($data['sp_log'], $data, $dn_id);

	} else {

		if(empty($data['content'])){
  		$data['content'] ='Страница не загружена, проверьте ссылку. Если ссылка на сайт открывается у вас в браузере то сообщите разработчику модуля эту ссылку для проверки и устранения проблемы.';
  	}

  	//для деления ссылок по спискам.
  	if($data['http_code'] > 302){ 
  		$value['http_code'] = $data['http_code'];
  	} 

  	$this->log($data['sp_log'], $data, $dn_id);
	}

	return $value;    
}

//Фунциял записи страницы в кеш.
public function putCachePageOnFile($data, $link, $dn_id){
	//директория хранения кеша.
	$cache_dir = DIR_APPLICATION.'simplepars/cache_page/'.$dn_id.'/';
	//Имя конкретной странице кеша, равна ключу ссылок данного донора.
	$file = md5($dn_id.$link).'.txt';
	//Полный путь к файлу. 
	$file = $cache_dir.$file;

	//Делаем метку что страница из кеша и записываем в файл.
	$text = '###########################################################'.PHP_EOL.
					'# ВНИМАНИЕ!!! Страница взята из кеша модуля SimplePars!!! #'.PHP_EOL.
					'# Дата создания кеша - '.date("Y-m-d H:i:s").'                #'.PHP_EOL.
					'###########################################################'.PHP_EOL.PHP_EOL.$data['content'];
	file_put_contents($file, $text);
	$logs = ['url' => $link, 'file' => $file];
	//сообшаем о создании файла кеша.
	$this->log('cache_file_add', $logs, $dn_id);
}

//данная фунция проверяет есть ли закешированная страница, если да то возврашает ее. Если нет отдает false
public function getCachePageFromFile($link, $browser, $dn_id){
	//имитация нормального ответа курла.
	$data['url'] = $link;
  $data['content_type'] = 'text/html; charset=utf-8';
  $data['http_code'] = '200';
	$data['errno'] = '0';
  $data['errmsg'] = '';
  $data['sp_log'] = 'log_cache';
  $data['browser'] = $browser;
  $data['content'] = '';

  //составляем путь к предполагаемому файлу кеша.
  $file = md5($dn_id.$link).'.txt';
  $file = DIR_APPLICATION.'simplepars/cache_page/'.$dn_id.'/'.$file;

  //проверяем есть ли такой файл.
  if (file_exists($file)) {
  	$data['content'] = file_get_contents($file);
  }else{
  	$data = false;
  }
  #$this->wtfarrey($data);
  return $data;
}

############################################################################################
############################################################################################
#						Другие фунции
############################################################################################
############################################################################################

//Фунция проучения настроек поставшика.
public function getSetting($dn_id){
	$setting = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_setting WHERE `dn_id`=".(int)$dn_id);
  $setting = $setting->row;
  #$this->wtfarrey($setting);
  return $setting;
}

public function checkEngine(){
	$engine = 'ocstore';

	//Начинаем запросы для определения версии движка магазина
	$meta_h1 = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."category_description` LIKE 'meta_h1'");
	if ($meta_h1->num_rows == 0) {
		$engine = 'opencart';
	}

	#опеределяем релиз
	$rl = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."url_alias'");
	if ($rl->num_rows == 0) {
		$engine .= 3;
	} else {
		$engine .= 2;
	}
	#$this->wtfarrey($engine);
	return $engine;
}

//получаем язык по умолчанию в админке.
public function getLangDef(){
	$language_id = $this->db->query("SELECT language_id FROM ".DB_PREFIX."language WHERE status=1");
	$language_id = $language_id->row['language_id'];
	if(empty($language_id)){ $language_id = 0;}
	return $language_id;
}

public function checkCronTable(){
	$cron = $this->db->query("SELECT * FROM `".DB_PREFIX."pars_cron` WHERE `id`= 1");
	if($cron->num_rows != 1){
		$this->db->query("INSERT INTO `".DB_PREFIX."pars_cron` SET `permit` = 'stop'");
	}
}

//Фунция проучения производителей и их id
public function getManufs(){
	$manufs = $this->db->query("SELECT `manufacturer_id` as `id`,`name` FROM `".DB_PREFIX."manufacturer`");
  $manufs = $manufs->rows;
  #$this->wtfarrey($manufs);
  return $manufs;
}

//Копия фунции чека категорий, скопировал для изучения.
public function repairCategories($parent_id = 0) {
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE parent_id = '" . (int)$parent_id . "'");

	foreach ($query->rows as $category) {
		// Delete the path below the current one
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category['category_id'] . "'");

		// Fix for records with no paths
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$parent_id . "' ORDER BY level ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category['category_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category['category_id'] . "', `path_id` = '" . (int)$category['category_id'] . "', level = '" . (int)$level . "'");

		$this->repairCategories($category['category_id']);
	}
}

//Создаем базу парсинга в im
public function createDbPrsetup($dn_id){
	$this->db->query("INSERT INTO ". DB_PREFIX ."pars_prsetup
  	SET dn_id=".(int)$dn_id.",
  	model='',
  	sku='',
  	name='',
  	price='',
  	quant='',
  	quant_d='101',
  	manufac='',
  	manufac_d='0',
  	des='',
  	des_d='',
  	cat='',
  	cat_d='',
  	img='',
  	img_d='',
  	img_dir='product',
  	attr=''");
}

//Создаем базу ,браузера
public function createDbBrowser($dn_id){
	$this->db->query("INSERT INTO ". DB_PREFIX ."pars_browser SET	dn_id=".(int)$dn_id);
}

//пауза парсинга микро секунды.
public function timeSleep($times){
	#$time = 0;

	if($times !== 0){

		$time = explode('-', $times);
		$time = array_filter($time);

		if(empty($time[0])){
			$time[0] = 0;
		}
		$time[0] = str_replace(',', '.', $time[0]);
		$time[0] = (float)$time[0];
		$time[0] = ($time[0]*1000000);

		if(empty($time[1])){
			usleep($time[0]);
		}else{
			$time[1] = str_replace(',', '.', $time[1]);
			$time[1] = (float)$time[1];
			$time[1] = ($time[1]*1000000);
			$rand_t = rand($time[0], $time[1]);
			usleep($rand_t);
		}


	}
	#$this->wtfarrey($time);
}

//Экспорт формы поставшика.
public function getExportForm($links,$dn_id){

	$finish = '';
	$data['setting'] = $this->getSetting($dn_id);

	$param = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_param WHERE dn_id=".$dn_id." ORDER BY `id` ASC");
  $data['param'] = $param->rows;

	$replace = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_replace WHERE `dn_id`=".(int)$dn_id." ORDER BY `id` ASC");
	$data['replace'] = $replace->rows;

	$createcsv = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_createcsv WHERE `dn_id`=".(int)$dn_id." ORDER BY `id` ASC");
	$data['createcsv'] = $createcsv->rows;

	$prsetup = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_prsetup WHERE `dn_id`=".(int)$dn_id." ORDER BY `id` ASC");
	$data['prsetup'] = $prsetup->row;

	$browser = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_browser WHERE `dn_id`=".(int)$dn_id." ORDER BY `id` ASC");
	$data['browser'] = $browser->row;

	$proxy_list = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_proxy_list WHERE `dn_id`=".(int)$dn_id." ORDER BY `id` ASC");
	$data['proxy_list'] = $proxy_list->rows;

	$pars_link_list = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_link_list WHERE `dn_id`=".(int)$dn_id." ORDER BY `id` ASC");
	$data['pars_link_list'] = $pars_link_list->rows;

	if($links === 0){

	}elseif($links === 2){
		$pars_sen_link = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_sen_link WHERE `dn_id`=".(int)$dn_id." ORDER BY `id` ASC");
		$data['pars_sen_link'] = $pars_sen_link->rows;
	}elseif($links === 1){
		$pars_link = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_link WHERE `dn_id`=".(int)$dn_id." ORDER BY `id` ASC");
		$data['pars_link'] = $pars_link->rows;
	}elseif($links === 3){
		$pars_sen_link = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_sen_link WHERE `dn_id`=".(int)$dn_id." ORDER BY `id` ASC");
		$data['pars_sen_link'] = $pars_sen_link->rows;

		$pars_link = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_link WHERE `dn_id`=".(int)$dn_id." ORDER BY `id` ASC");
		$data['pars_link'] = $pars_link->rows;
	}

	//Финальный массив на отдачу.
	$finish = json_encode($data);

	return $finish;
}

//ипорт формы
public function importFrom($data, $dn_id){
	$data = json_decode($data, true);
	#$this->wtfarrey($data);
	if(is_array($data)){

		//ну что тут начинаем загружать.
		if(!empty($data['setting'])){

			//проверяем есть ли указание кодировки
			if(!isset($data['setting']['link_list'])){ $data['setting']['link_list']=''; }
			if(!isset($data['setting']['link_error'])){ $data['setting']['link_error']=''; }
			if(!isset($data['setting']['pre_view_param'])){ $data['setting']['pre_view_param']=1; }
			if(!isset($data['setting']['pre_view_syntax'])){ $data['setting']['pre_view_syntax']=1; }
			if(empty($data['setting']['csv_charset'])){ $data['setting']['csv_charset']=1; }
			if(empty($data['setting']['r_made_url'])){ $data['setting']['r_made_url']=1; }
			if(empty($data['setting']['r_made_meta'])){ $data['setting']['r_made_meta']=0; }
			if(empty($data['setting']['r_cat_perent'])){ $data['setting']['r_cat_perent']=0; }
			if(empty($data['setting']['r_cat_made_url'])){ $data['setting']['r_cat_made_url']=1; }
			if(empty($data['setting']['r_cat_made_meta'])){ $data['setting']['r_cat_made_meta']=0; }
			if(empty($data['setting']['r_manufac_made_url'])){ $data['setting']['r_manufac_made_url']=1; }
			if(empty($data['setting']['r_manufac_made_meta'])){ $data['setting']['r_manufac_made_meta']=0; }
			if(empty($data['setting']['page_cou_link'])){ $data['setting']['page_cou_link'] = 5000; }
			if(empty($data['setting']['r_attr_group'])){ $data['setting']['r_attr_group'] = 1; }
			if(empty($data['setting']['r_opt'])){ $data['setting']['r_opt'] = 0; }
			if(empty($data['setting']['r_price_spec_groups'])){ $data['setting']['r_price_spec_groups'] = ''; }
			if(empty($data['setting']['r_price_spec_date_start'])){ $data['setting']['r_price_spec_date_start'] = ''; }
			if(empty($data['setting']['r_price_spec_date_end'])){ $data['setting']['r_price_spec_date_end'] = ''; }
			if(empty($data['setting']['r_status_zero'])){ $data['setting']['r_status_zero'] = 5; }
			if(empty($data['setting']['filter_round_param'])){ $data['setting']['filter_round_param'] = ''; }
			if(empty($data['setting']['filter_round_depth'])){ $data['setting']['filter_round_depth'] = ''; }
			if(empty($data['setting']['filter_round_slash'])){ $data['setting']['filter_round_slash'] = 0; }
			if(!isset($data['setting']['filter_round_domain'])){ $data['setting']['filter_round_domain'] = 1; }
			if(empty($data['setting']['filter_link_param'])){ $data['setting']['filter_link_param'] = ''; }
			if(empty($data['setting']['filter_link_depth'])){ $data['setting']['filter_link_depth'] = ''; }
			if(empty($data['setting']['filter_link_slash'])){ $data['setting']['filter_link_slash'] = 0; }
			if(!isset($data['setting']['filter_link_domain'])){ $data['setting']['filter_link_domain'] = 1; }
			if(empty($data['setting']['logs_reverse'])){ $data['setting']['logs_reverse'] = 0; }
			if(empty($data['setting']['logs_mb'])){ $data['setting']['logs_mb'] = 25; }

			if(empty($data['setting']['r_upc'])){ $data['setting']['r_upc'] = 0; }
			if(empty($data['setting']['r_ean'])){ $data['setting']['r_ean'] = 0; }
			if(empty($data['setting']['r_jan'])){ $data['setting']['r_jan'] = 0; }
			if(empty($data['setting']['r_isbn'])){ $data['setting']['r_isbn'] = 0; }
			if(empty($data['setting']['r_mpn'])){ $data['setting']['r_mpn'] = 0; }
			if(empty($data['setting']['r_location'])){ $data['setting']['r_location'] = 0; }
			if(empty($data['setting']['r_minimum'])){ $data['setting']['r_minimum'] = 0; }
			if(empty($data['setting']['r_subtract'])){ $data['setting']['r_subtract'] = 0; }
			if(empty($data['setting']['r_length'])){ $data['setting']['r_length'] = 0; }
			if(empty($data['setting']['r_width'])){ $data['setting']['r_width'] = 0; }
			if(empty($data['setting']['r_height'])){ $data['setting']['r_height'] = 0; }
			if(empty($data['setting']['r_length_class_id'])){ $data['setting']['r_length_class_id'] = 0; }
			if(empty($data['setting']['r_weight'])){ $data['setting']['r_weight'] = 0; }
			if(empty($data['setting']['r_weight_class_id'])){ $data['setting']['r_weight_class_id'] = 0; }
			if(empty($data['setting']['r_status'])){ $data['setting']['r_status'] = 0; }
			if(empty($data['setting']['r_sort_order'])){ $data['setting']['r_sort_order'] = 0; }
			if(empty($data['setting']['type_grab'])){ $data['setting']['type_grab'] = 1;}
			if(empty($data['setting']['thread'])){ $data['setting']['thread'] = 1;}



			//Дополнительные преобразования перед записью в базу
			if(empty($data['setting']['grans_permit'])){ $data['setting']['grans_permit'] = 0;}

			//Если не выбран магазин то все магазины по умолчани.
			if (empty($data['setting']['r_store'])) {
				$data['setting']['r_store'] = '';
				$temp_s = $this->getAllStore();
				foreach ($temp_s as $key_ts => $t_s) {
					if ($key_ts != 0) { $data['setting']['r_store'] .= ','.$t_s['store_id']; } else { $data['setting']['r_store'] = $t_s['store_id']; }
				}
			}

			#Если убрали все галочки в языке тогда записываем выбрать все языки в магазине.
			if(empty($data['setting']['r_lang'])) {
				$data['setting']['r_lang'] = '';
				$temp_l = $this->getAllLang();
				foreach ($temp_l as $key_tl => $t_l) {
					if ($key_tl != 0) { $data['setting']['r_lang'] .= ','.$t_l['language_id']; } else { $data['setting']['r_lang'] = $t_l['language_id']; }
				}
			}
			//Определяем версию движка
			$engine = $this->checkEngine();
			$data['setting']['vers_op'] = $engine;

			$this->db->query("UPDATE `".DB_PREFIX."pars_setting` SET
				`pre_view_param`='".$this->db->escape($data['setting']['pre_view_param'])."',
				`pre_view_syntax`='".$this->db->escape($data['setting']['pre_view_syntax'])."',
				`start_link`='".$this->db->escape($data['setting']['start_link'])."',
				`link_list`='".$this->db->escape($data['setting']['link_list'])."',
				`link_error`='".$this->db->escape($data['setting']['link_error'])."',
				`page_cou_link`='".$this->db->escape($data['setting']['page_cou_link'])."',
				`pars_stop`='".$this->db->escape($data['setting']['pars_stop'])."',
				`csv_name`='".$this->db->escape($data['setting']['csv_name'])."',
				`csv_delim`='".$this->db->escape($data['setting']['csv_delim'])."',
				`csv_escape`='".$this->db->escape($data['setting']['csv_escape'])."',
				`csv_charset`='".$this->db->escape($data['setting']['csv_charset'])."',
				`pars_pause`='".$this->db->escape($data['setting']['pars_pause'])."',
				`type_grab`='".$this->db->escape($data['setting']['type_grab'])."',
				`thread`='".$this->db->escape($data['setting']['thread'])."',
				`filter_round_yes`='".$this->db->escape($data['setting']['filter_round_yes'])."',
				`filter_round_no`='".$this->db->escape($data['setting']['filter_round_no'])."',
				`filter_round_method`='".$this->db->escape($data['setting']['filter_round_method'])."',
				`filter_round_param`='".$this->db->escape($data['setting']['filter_round_param'])."',
				`filter_round_depth`='".$this->db->escape($data['setting']['filter_round_depth'])."',
				`filter_round_slash`='".$this->db->escape($data['setting']['filter_round_slash'])."',
				`filter_round_domain`='".$this->db->escape($data['setting']['filter_round_domain'])."',
				`filter_link_yes`='".$this->db->escape($data['setting']['filter_link_yes'])."',
				`filter_link_no`='".$this->db->escape($data['setting']['filter_link_no'])."',
				`filter_link_method`='".$this->db->escape($data['setting']['filter_link_method'])."',
				`filter_link_param`='".$this->db->escape($data['setting']['filter_link_param'])."',
				`filter_link_depth`='".$this->db->escape($data['setting']['filter_link_depth'])."',
				`filter_link_slash`='".$this->db->escape($data['setting']['filter_link_slash'])."',
				`filter_link_domain`='".$this->db->escape($data['setting']['filter_link_domain'])."',
				`action`='".$this->db->escape($data['setting']['action'])."',
				`sid`='".$this->db->escape($data['setting']['sid'])."',
				`grans_permit`='".$this->db->escape($data['setting']['grans_permit'])."',
				`r_store`='".$this->db->escape($data['setting']['r_store'])."',
				`r_lang`='".$this->db->escape($data['setting']['r_lang'])."',
				`r_model`='".$this->db->escape($data['setting']['r_model'])."',
				`r_sku`='".$this->db->escape($data['setting']['r_sku'])."',
				`r_name`='".$this->db->escape($data['setting']['r_name'])."',
				`r_made_url`='".$this->db->escape($data['setting']['r_made_url'])."',
				`r_made_meta`='".$this->db->escape($data['setting']['r_made_meta'])."',
				`r_price`='".$this->db->escape($data['setting']['r_price'])."',
				`r_price_spec_groups`='".$this->db->escape($data['setting']['r_price_spec_groups'])."',
				`r_price_spec_date_start`='".$this->db->escape($data['setting']['r_price_spec_date_start'])."',
				`r_price_spec_date_end`='".$this->db->escape($data['setting']['r_price_spec_date_end'])."',
				`r_quant`='".$this->db->escape($data['setting']['r_quant'])."',
				`r_status_zero`='".$this->db->escape($data['setting']['r_status_zero'])."',
				`r_status`='".$this->db->escape($data['setting']['r_status'])."',
				`r_manufac`='".$this->db->escape($data['setting']['r_manufac'])."',
				`r_manufac_made_url`='".$this->db->escape($data['setting']['r_manufac_made_url'])."',
				`r_manufac_made_meta`='".$this->db->escape($data['setting']['r_manufac_made_meta'])."',
				`r_des`='".$this->db->escape($data['setting']['r_des'])."',
				`r_cat`='".$this->db->escape($data['setting']['r_cat'])."',
				`r_cat_perent`='".$this->db->escape($data['setting']['r_cat_perent'])."',
				`r_cat_made_url`='".$this->db->escape($data['setting']['r_cat_made_url'])."',
				`r_cat_made_meta`='".$this->db->escape($data['setting']['r_cat_made_meta'])."',
				`r_img`='".$this->db->escape($data['setting']['r_img'])."',
				`r_img_dir`='".$this->db->escape($data['setting']['r_img_dir'])."',
				`r_attr`='".$this->db->escape($data['setting']['r_attr'])."',
				`r_attr_group`='".$this->db->escape($data['setting']['r_attr_group'])."',
				`r_opt`='".$this->db->escape($data['setting']['r_opt'])."',
				`r_upc`='".$this->db->escape($data['setting']['r_upc'])."',
				`r_ean`='".$this->db->escape($data['setting']['r_ean'])."',
				`r_jan`='".$this->db->escape($data['setting']['r_jan'])."',
				`r_isbn`='".$this->db->escape($data['setting']['r_isbn'])."',
				`r_mpn`='".$this->db->escape($data['setting']['r_mpn'])."',
				`r_location`='".$this->db->escape($data['setting']['r_location'])."',
				`r_minimum`='".$this->db->escape($data['setting']['r_minimum'])."',
				`r_subtract`='".$this->db->escape($data['setting']['r_subtract'])."',
				`r_length`='".$this->db->escape($data['setting']['r_length'])."',
				`r_width`='".$this->db->escape($data['setting']['r_width'])."',
				`r_height`='".$this->db->escape($data['setting']['r_height'])."',
				`r_length_class_id`='".$this->db->escape($data['setting']['r_length_class_id'])."',
				`r_weight`='".$this->db->escape($data['setting']['r_weight'])."',
				`r_weight_class_id`='".$this->db->escape($data['setting']['r_weight_class_id'])."',
				`r_status`='".$this->db->escape($data['setting']['r_status'])."',
				`r_sort_order`='".$this->db->escape($data['setting']['r_sort_order'])."',
				`logs_reverse`='".$this->db->escape($data['setting']['logs_reverse'])."',
				`logs_mb`='".$this->db->escape($data['setting']['logs_mb'])."',
				`vers_op` = '".$this->db->escape($data['setting']['vers_op'])."'
				WHERE `dn_id`=".(int)$dn_id
			);
		}

		//Удаляем старые настройки
		$this->db->query("DELETE FROM `".DB_PREFIX."pars_param` WHERE `dn_id`=".(int)$dn_id);
		$this->db->query("DELETE FROM `".DB_PREFIX."pars_replace` WHERE `dn_id`=".(int)$dn_id);
		$this->db->query("DELETE FROM `".DB_PREFIX."pars_createcsv` WHERE `dn_id`=".(int)$dn_id);
		$this->db->query("DELETE FROM `".DB_PREFIX."pars_prsetup` WHERE `dn_id`=".(int)$dn_id);
		$this->db->query("DELETE FROM `".DB_PREFIX."pars_sen_link` WHERE `dn_id`=".(int)$dn_id);
		$this->db->query("DELETE FROM `".DB_PREFIX."pars_link` WHERE `dn_id`=".(int)$dn_id);
		$this->db->query("DELETE FROM `".DB_PREFIX."pars_link_list` WHERE `dn_id`=".(int)$dn_id);
		$this->db->query("DELETE FROM `".DB_PREFIX."pars_proxy_list` WHERE `dn_id`=".(int)$dn_id);
		$this->db->query("DELETE FROM `".DB_PREFIX."pars_browser` WHERE `dn_id`=".(int)$dn_id);


		#Создаем таблицу Prsetup
		$this->createDbPrsetup($dn_id);
		#Создаем таблицу Браузера
		$this->createDbBrowser($dn_id);

		//Тут посложнее из за того что все привязано к ключам автоинкремент. Начнем плясать от параметров.
		$param_id = [];
		if(!empty($data['param'])){
			//создаем заново.
			foreach($data['param'] as $key => $param){
				//Из за гребанной совместимости версий. Боль в моей заднице.
				if(empty($param['skip_where'])){
					$sql = '';
				}else{
					$sql ="`with_teg`=".$this->db->escape($param['with_teg']).",
					`skip_enter`='".$this->db->escape($param['skip_enter'])."',
					`skip_where`=".$this->db->escape($param['skip_where']).",
					`reverse`=".$this->db->escape($param['reverse']).",";
				}

				$this->db->query("INSERT INTO `".DB_PREFIX."pars_param` SET
					`dn_id`='".$this->db->escape($dn_id)."',
					`name`='".$this->db->escape($param['name'])."',
					`start`='".$this->db->escape($param['start'])."',
					`stop`='".$this->db->escape($param['stop'])."',
					`type`='".$this->db->escape($param['type'])."',
					".$sql."
					`base_id`='".$this->db->escape($param['base_id'])."',
					`delim`='".$this->db->escape($param['delim'])."'
				");

				//Получаем новый id границы парсинга и засовываем в врменный массив.
				$param_id[$key] = ['old_id' => $param['id'], 'new_id' => $this->db->getLastId()];
			}

			//теперь присваеваем правильные id родительских границ.
			$param_bases = $this->db->query("SELECT * FROM ".DB_PREFIX."pars_param WHERE dn_id=".(int)$dn_id." ORDER BY `id` ASC");
			if($param_bases->num_rows > 0){
				$param_bases = $param_bases->rows;

				//обновляем id родителя границы.
				foreach($param_bases as $param_base){
					$base_id=0;
					//Выбираем правильный id радителя.
					foreach($param_id as $value){
						if($param_base['base_id'] == $value['old_id']){
							$base_id = $value['new_id'];
						}
					}

					$this->db->query("UPDATE `".DB_PREFIX."pars_param` SET `base_id`=".(int)$base_id." WHERE `id`=".(int)$param_base['id']);

				}
			}
		}

		//Загружаем таблицу реплейса.
		if(!empty($data['replace'])){

			//Создаем заново.
			foreach($data['replace'] as $replace){

				//Проверяем есть ли правила хеширования
				if(empty($replace['hash'])){ $replace['hash'] = 0; }

				//Есть ли математическая фунция
				if(empty($replace['arithm'])){ $replace['arithm'] = ''; }

				//получаем актуальные id параметров парсинга
				foreach($param_id as $value){
					if($replace['param_id'] == $value['old_id']){
						$replace['param_id'] = $value['new_id'];
					}
				}

				$this->db->query("INSERT INTO `".DB_PREFIX."pars_replace` SET
					`dn_id`='".(int)$dn_id."',
					`param_id`='".$this->db->escape($replace['param_id'])."',
					`text_start`='".$this->db->escape($replace['text_start'])."',
					`text_stop`='".$this->db->escape($replace['text_stop'])."',
					`rules`='".$this->db->escape($replace['rules'])."',
					`hash`=".(int)$replace['hash'].", arithm='".$this->db->escape($replace['arithm'])."'"
				);
			}
		}

		//таблица составления прайса pars_createcsv
		if(!empty($data['createcsv'])){

			//создаем заново
			foreach($data['createcsv'] as $createcsv){

				//получаем актуальные id параметров парсинга
				foreach($param_id as $value){
					#переходная закладка. Удалить через пару месяцев, когда все перейдут на новую версию модуля.
					#########################################################################################
					if(!empty($createcsv['param_id'])){
						if($createcsv['param_id'] == 'link'){
							$createcsv['value'] = '{link}';
						}else{
							$createcsv['value'] = '{gran_'.$createcsv['param_id'].'}';
							unset($createcsv['param_id']);
						}

					}
					#########################################################################################
					$createcsv['value'] = str_replace('{gran_'.$value['old_id'].'}', '{gran_'.$value['new_id'].'}', $createcsv['value']);
				}

				if(empty($createcsv['csv_column'])){ $createcsv['csv_column'] = ''; }
				$this->db->query("INSERT INTO `".DB_PREFIX."pars_createcsv` SET
					`dn_id`=".(int)$dn_id.",
					`name`='".$this->db->escape($createcsv['name'])."',
					`value`='".$this->db->escape($createcsv['value'])."',
					`csv_column`='".$this->db->escape($createcsv['csv_column'])."'");
			}
		}

		//таблица парсинга в им pars_prsetup
		if(!empty($data['prsetup'])){

			//получаем актуальные id параметров парсинга
			foreach($param_id as $value){
				#переделываем данные массива.
				foreach($data['prsetup'] as $key => $prsetup){
					$data['prsetup'][$key] = str_replace('{gran_'.$value['old_id'].'}', '{gran_'.$value['new_id'].'}', $prsetup);
				}
			}

			if(empty($data['prsetup']['price_spec'])){ $data['prsetup']['price_spec'] = ''; }

			//опции
			if(empty($data['prsetup']['opt_name'])){ $data['prsetup']['opt_name'] = ''; }
			if(empty($data['prsetup']['opt_value'])){ $data['prsetup']['opt_value'] = ''; }
			if(empty($data['prsetup']['opt_price'])){ $data['prsetup']['opt_price'] = ''; }
			if(empty($data['prsetup']['opt_quant'])){ $data['prsetup']['opt_quant'] = ''; }
			if(!isset($data['prsetup']['opt_quant_d'])){ $data['prsetup']['opt_quant_d'] = '10'; }
			if(empty($data['prsetup']['opt_data'])){ $data['prsetup']['opt_data'] = ''; }
			
			if(empty($data['prsetup']['grans_permit_list'])){ $data['prsetup']['grans_permit_list'] = ''; }

			if(empty($data['prsetup']['seo_url'])){ $data['prsetup']['seo_url'] = ''; }
			if(empty($data['prsetup']['seo_h1'])){ $data['sprsetup']['seo_h1'] = ''; }
			if(empty($data['prsetup']['seo_title'])){ $data['prsetup']['seo_title'] = ''; }
			if(empty($data['prsetup']['seo_desc'])){ $data['prsetup']['seo_desc'] = ''; }
			if(empty($data['prsetup']['seo_keyw'])){ $data['prsetup']['seo_keyw'] = ''; }

			if(empty($data['prsetup']['cat_seo_url'])){ $data['prsetup']['cat_seo_url'] = ''; }
			if(empty($data['prsetup']['cat_seo_h1'])){ $data['sprsetup']['cat_seo_h1'] = ''; }
			if(empty($data['prsetup']['cat_seo_title'])){ $data['prsetup']['cat_seo_title'] = ''; }
			if(empty($data['prsetup']['cat_seo_desc'])){ $data['prsetup']['cat_seo_desc'] = ''; }
			if(empty($data['prsetup']['cat_seo_keyw'])){ $data['prsetup']['cat_seo_keyw'] = ''; }

			if(empty($data['prsetup']['manuf_seo_url'])){ $data['prsetup']['manuf_seo_url'] = ''; }
			if(empty($data['prsetup']['manuf_seo_h1'])){ $data['sprsetup']['manuf_seo_h1'] = ''; }
			if(empty($data['prsetup']['manuf_seo_title'])){ $data['prsetup']['manuf_seo_title'] = ''; }
			if(empty($data['prsetup']['manuf_seo_desc'])){ $data['prsetup']['manuf_seo_desc'] = ''; }
			if(empty($data['prsetup']['manuf_seo_keyw'])){ $data['prsetup']['manuf_seo_keyw'] = ''; }

			if(!isset($data['prsetup']['upc'])){ $data['prsetup']['upc'] = ''; }
			if(!isset($data['prsetup']['ean'])){ $data['prsetup']['ean'] = ''; }
			if(!isset($data['prsetup']['jan'])){ $data['prsetup']['jan'] = ''; }
			if(!isset($data['prsetup']['isbn'])){ $data['prsetup']['isbn'] = ''; }
			if(!isset($data['prsetup']['mpn'])){ $data['prsetup']['mpn'] = ''; }
			if(!isset($data['prsetup']['location'])){ $data['prsetup']['location'] = ''; }
			if(empty($data['prsetup']['minimum'])){ $data['prsetup']['minimum'] = 1; }
			if(!isset($data['prsetup']['subtract'])){ $data['prsetup']['subtract'] = 1; }
			if(empty($data['prsetup']['length'])){ $data['prsetup']['length'] = 0.00; }
			if(empty($data['prsetup']['width'])){ $data['prsetup']['width'] = 0.00; }
			if(empty($data['prsetup']['height'])){ $data['prsetup']['height'] = 0.00; }
			if(empty($data['prsetup']['length_class_id'])){ $data['prsetup']['length_class_id'] = 1; }
			if(empty($data['prsetup']['weight'])){ $data['prsetup']['weight'] = 0.00; }
			if(empty($data['prsetup']['weight_class_id'])){ $data['prsetup']['weight_class_id'] = 1; }
			if(!isset($data['prsetup']['status'])){ $data['prsetup']['status'] = 1; }
			if(empty($data['prsetup']['sort_order'])){ $data['prsetup']['sort_order'] = 0; }

			//Создаем заново.
			$this->db->query("UPDATE `".DB_PREFIX."pars_prsetup` SET
				`model`='".$this->db->escape($data['prsetup']['model'])."',
				`sku`='".$this->db->escape($data['prsetup']['sku'])."',
				`name`='".$this->db->escape($data['prsetup']['name'])."',
				`price`='".$this->db->escape($data['prsetup']['price'])."',
				`price_spec`='".$this->db->escape($data['prsetup']['price_spec'])."',
				`quant`='".$this->db->escape($data['prsetup']['quant'])."',
				`quant_d`='".$this->db->escape($data['prsetup']['quant_d'])."',
				`des`='".$this->db->escape($data['prsetup']['des'])."',
				`des_d`='".$this->db->escape($data['prsetup']['des_d'])."',
				`manufac`='".$this->db->escape($data['prsetup']['manufac'])."',
				`manufac_d`='".$this->db->escape($data['prsetup']['manufac_d'])."',
				`cat`='".$this->db->escape($data['prsetup']['cat'])."',
				`cat_d`='".$this->db->escape($data['prsetup']['cat_d'])."',
				`img`='".$this->db->escape($data['prsetup']['img'])."',
				`img_d`='".$this->db->escape($data['prsetup']['img_d'])."',
				`img_dir`='".$this->db->escape($data['prsetup']['img_dir'])."',
				`attr`='".$this->db->escape($data['prsetup']['attr'])."',
				`opt_name`='".$this->db->escape($data['prsetup']['opt_name'])."',
				`opt_value`='".$this->db->escape($data['prsetup']['opt_value'])."',
				`opt_price`='".$this->db->escape($data['prsetup']['opt_price'])."',
				`opt_quant`='".$this->db->escape($data['prsetup']['opt_quant'])."',
				`opt_quant_d`='".$this->db->escape($data['prsetup']['opt_quant_d'])."',
				`opt_data`='".$this->db->escape($data['prsetup']['opt_data'])."',
				`grans_permit_list`='".$this->db->escape($data['prsetup']['grans_permit_list'])."',
				`upc`='".$this->db->escape($data['prsetup']['upc'])."',
				`ean`='".$this->db->escape($data['prsetup']['ean'])."',
				`jan`='".$this->db->escape($data['prsetup']['jan'])."',
				`isbn`='".$this->db->escape($data['prsetup']['isbn'])."',
				`mpn`='".$this->db->escape($data['prsetup']['mpn'])."',
				`location`='".$this->db->escape($data['prsetup']['location'])."',
				`minimum`='".$this->db->escape($data['prsetup']['minimum'])."',
				`subtract`='".$this->db->escape($data['prsetup']['subtract'])."',
				`length`='".$this->db->escape($data['prsetup']['length'])."',
				`width`='".$this->db->escape($data['prsetup']['width'])."',
				`height`='".$this->db->escape($data['prsetup']['height'])."',
				`length_class_id`='".$this->db->escape($data['prsetup']['length_class_id'])."',
				`weight`='".$this->db->escape($data['prsetup']['weight'])."',
				`weight_class_id`='".$this->db->escape($data['prsetup']['weight_class_id'])."',
				`status`='".$this->db->escape($data['prsetup']['status'])."',
				`sort_order`='".$this->db->escape($data['prsetup']['sort_order'])."',
				`seo_url`='".$this->db->escape($data['prsetup']['seo_url'])."',
				`seo_h1`='".$this->db->escape($data['prsetup']['seo_h1'])."',
				`seo_title`='".$this->db->escape($data['prsetup']['seo_title'])."',
				`seo_desc`='".$this->db->escape($data['prsetup']['seo_desc'])."',
				`seo_keyw`='".$this->db->escape($data['prsetup']['seo_keyw'])."',
				`cat_seo_url`='".$this->db->escape($data['prsetup']['cat_seo_url'])."',
				`cat_seo_h1`='".$this->db->escape($data['prsetup']['cat_seo_h1'])."',
				`cat_seo_title`='".$this->db->escape($data['prsetup']['cat_seo_title'])."',
				`cat_seo_desc`='".$this->db->escape($data['prsetup']['cat_seo_desc'])."',
				`cat_seo_keyw`='".$this->db->escape($data['prsetup']['cat_seo_keyw'])."',
				`manuf_seo_url`='".$this->db->escape($data['prsetup']['manuf_seo_url'])."',
				`manuf_seo_h1`='".$this->db->escape($data['prsetup']['manuf_seo_h1'])."',
				`manuf_seo_title`='".$this->db->escape($data['prsetup']['manuf_seo_title'])."',
				`manuf_seo_desc`='".$this->db->escape($data['prsetup']['manuf_seo_desc'])."',
				`manuf_seo_keyw`='".$this->db->escape($data['prsetup']['manuf_seo_keyw'])."'
				WHERE `dn_id`='".(int)$dn_id."'");
		}

		//Работа с сылками. pars_sen_link
		if(!empty($data['pars_sen_link'])){

			//Создаем заново
			foreach($data['pars_sen_link'] as $sen_link){
				if(!isset($link['scan_cron'])){ $link['scan_cron'] = '1'; }
				//Если импортируюи ссылки из старых версий модуля.
				$sen_link['key_md5'] = md5($dn_id.$sen_link['link']);
				$this->db->query("INSERT IGNORE INTO `".DB_PREFIX."pars_sen_link` SET
					`dn_id`=".(int)$dn_id.",
					`link`='".$this->db->escape($sen_link['link'])."',
					`key_md5`='".$this->db->escape($sen_link['key_md5'])."',
					`scan`='".$this->db->escape($sen_link['scan'])."',
					`scan_cron`='".$this->db->escape($link['scan_cron'])."'
				");
			}
		}

		//Работа с сылками. pars_link
		if(!empty($data['pars_link'])){

			//Создаем заново
			foreach($data['pars_link'] as $link){
				if(!isset($link['scan_cron'])){ $link['scan_cron'] = '1'; }
				if(!isset($link['list'])){ $link['list'] = '0'; }
				if(!isset($link['error'])){ $link['error'] = '0'; }
				//Если импортируюи ссылки из старых версий модуля.
				$link['key_md5'] = md5($dn_id.$link['link']);
				$this->db->query("INSERT IGNORE INTO `".DB_PREFIX."pars_link` SET
					`dn_id`=".(int)$dn_id.",
					`link`='".$this->db->escape($link['link'])."',
					`key_md5`='".$this->db->escape($link['key_md5'])."',
					`scan`='".$this->db->escape($link['scan'])."',
					`scan_cron`='".$this->db->escape($link['scan_cron'])."',
					`list`='".$this->db->escape($link['list'])."',
					`error`='".$this->db->escape($link['error'])."'
				");
			}
		}

		//Работа с списсками ссылок
		if(!empty($data['pars_link_list'])){

			//Создаем заново
			foreach($data['pars_link_list'] as $list){
				//переносим списки ссылок
				$this->db->query("INSERT INTO `".DB_PREFIX."pars_link_list` SET `dn_id`=".(int)$dn_id.", `name` = '".$this->db->escape($list['name'])."'");
			}
		}
		//Записываем браузер в базу данных
		if(!empty($data['browser'])){

			if(!isset($data['browser']['protocol_version'])){ $data['browser']['protocol_version'] = 2;}

			$this->db->query("UPDATE `".DB_PREFIX."pars_browser` SET
			`proxy_use`='".$this->db->escape($data['browser']['proxy_use'])."',
			`timeout`='".$this->db->escape($data['browser']['timeout'])."',
			`connect_timeout`='".$this->db->escape($data['browser']['timeout'])."',
			`protocol_version`='".$this->db->escape($data['browser']['protocol_version'])."',
			`header_get`='".$this->db->escape($data['browser']['header_get'])."',
			`followlocation`='".$this->db->escape($data['browser']['followlocation'])."',
			`cookie_use`='".$this->db->escape($data['browser']['cookie_use'])."',
			`cookie_session`='".$this->db->escape($data['browser']['cookie_session'])."',
			`user_agent_use`='".$this->db->escape($data['browser']['user_agent_use'])."',
			`user_agent_change`='".$this->db->escape($data['browser']['user_agent_change'])."',
			`user_agent_list`='".$this->db->escape($data['browser']['user_agent_list'])."',
			`header_use`='".$this->db->escape($data['browser']['header_use'])."',
			`header_change`='".$this->db->escape($data['browser']['header_change'])."',
			`header_list`='".$this->db->escape($data['browser']['header_list'])."',
			`cache_page`='".$this->db->escape($data['browser']['cache_page'])."',
			`ch_connect_timeout`='".$this->db->escape($data['browser']['ch_connect_timeout'])."',
			`ch_timeout`='".$this->db->escape($data['browser']['ch_timeout'])."',
			`ch_url`='".$this->db->escape($data['browser']['ch_url'])."',
			`ch_pattern`='".$this->db->escape($data['browser']['ch_pattern'])."'
			WHERE `dn_id`=".(int)$dn_id);

		}

		//Запись прокси серверов
		if(!empty($data['proxy_list'])){

			foreach ($data['proxy_list'] as $key => $proxy_list) {
				$this->db->query("INSERT INTO `".DB_PREFIX."pars_proxy_list` SET
				`dn_id`='".(int)$dn_id."',
				`proxy`='".$this->db->escape($proxy_list['proxy'])."',
				`status`='".$this->db->escape($proxy_list['status'])."'");
			}

		}

		$this->session->data['success'] = 'Форма успешно загружена';
	}else{
		$this->session->data['error'] = ' Ошибка файла настроек, настройки не были обновлены!';
	}#is_arrey

}

//получение ссылки по ее id
public function getUrlFromId($url_id){
	$url = $this->db->query("SELECT * FROM " . DB_PREFIX . "pars_link WHERE `id`=".(int)$url_id);
	return $url->row;
}

//получение id ссылки по ее телу (что ли так написать :))
public function getUrlId($url){
	$url = $this->db->query("SELECT * FROM " . DB_PREFIX . "pars_link WHERE `link`='".$this->db->escape($url)."'");
	return $url->row;
}

//Фунци для скачивания файлов.
public function dwFile($who, $dn_id) {

	//получаем настройки
	$setting = $this->getSetting($dn_id);

	//проверяем что нам нужно отдать.
	if ($who == 'csv') {
		$file = './uploads/'.$setting['csv_name'].'.csv';
	} elseif ($who == 'logs') {
		$file = DIR_LOGS.'simplepars_id-'.$dn_id.'.log';
	} else {
		$file = false;
	}

	if (file_exists($file)) {
  // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
  // если этого не сделать файл будет читаться в память полностью!
  if (ob_get_level()) {
    ob_end_clean();
  }

  // заставляем браузер показать окно сохранения файла
  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename=' . basename($file));
  header('Content-Transfer-Encoding: binary');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($file));
  // читаем файл и отправляем его пользователю
  readfile($file);

  exit;

	}
}

//Фунция удаления файла.
public function delFile($dn_id) {

	//получаем настройки
	$setting = $this->getSetting($dn_id);

	//Формируем путь к прайсу который нужно грохнуть.
	$file = './uploads/'.$setting['csv_name'].'.csv';

	//Проверяем есть ли такой прайс.
	if (file_exists($file)) {
		unlink($file);
	}

}

//Конвертер байты в килобайты, мегабайты, гигбайты , терабаты.
public function convertBytes($bytes){
	
	if ( $bytes < 1000 * 1024 ) {
	
	  return number_format( $bytes / 1024, 2 ) . " KB";
	
	}	elseif ( $bytes < 1000 * 1048576 ) {
	
	  return number_format( $bytes / 1048576, 2 ) . " MB";
	
	} elseif ( $bytes < 1000 * 1073741824 ) {
	
	  return number_format( $bytes / 1073741824, 2 ) . " GB";
	
	} else {
	
	  return number_format( $bytes / 1099511627776, 2 ) . " TB";
	
	}
}

//Вспомагательная фунция для колбек. И очишение от html
public function htmlview(&$value){

	$value = htmlspecialchars($value);
}

//Универсальная фунция ответа на ajax запросы. Надесь я смогу развернуть потенциал этой фунции.
public function answjs($status, $msg='', $arrey=''){
	$data['status'] = $status;
	$data['msg'] = $msg;
	$data['other'] = $arrey;
	exit(json_encode($data));
}

public function symbolToEn($text=''){
	//переводим русские символы в латиницу
  $symbol = [ "А"=>"a",	"Б"=>"b", "В"=>"v", "Г"=>"g",
		          "Д"=>"d",	"Е"=>"e", "Ё"=>"e", "Ж"=>"g",
		          "З"=>"z",	"И"=>"i", "Й"=>"J", "К"=>"k",
		          "Л"=>"l",	"М"=>"m", "Н"=>"n", "О"=>"o",
		          "П"=>"p",	"Р"=>"r", "С"=>"s", "Т"=>"t",
		          "У"=>"u",	"Ф"=>"f", "Х"=>"h", "Ц"=>"ts",
		          "Ч"=>"ch", "Ш"=>"sh", "Щ"=>"sch", "Ъ"=>"a",
		          "Ы"=>"y",	"Ь"=>"", "Э"=>"e", "Ю"=>"yu",
		          "Я"=>"ya",	"Ї"=>"ji", "Ґ"=>"g", "І"=>"I",
		          "а"=>"a",	"б"=>"b", "в"=>"v", "г"=>"g",
		          "д"=>"d",	"е"=>"e", "ё"=>"e", "ж"=>"g",
		          "з"=>"z",	"и"=>"i", "й"=>"j", "к"=>"k",
		          "л"=>"l",	"м"=>"m", "н"=>"n", "о"=>"o",
		          "п"=>"p",	"р"=>"r", "с"=>"s", "т"=>"t",
		          "у"=>"u",	"ф"=>"f", "х"=>"h", "ц"=>"ts",
		          "ч"=>"ch", "ш"=>"sh", "щ"=>"sch", "ъ"=>"a",
		          "ы"=>"y", "ь"=>"", "э"=>"e", "ю"=>"yu",
		          "я"=>"ya", "ї"=>"ji", "і"=>"i", "ґ"=>"g",
		          "Є"=>"e", "є"=>"e", "ў"=>"u", "Ў"=>"u",
		          "і"=>"i", "І"=>"i", "«"=>"-", "»"=>"-",
		          "—"=>"-", "–"=>"-", " "=>"-", "“"=>"-",
		          "”"=>"-", "Ā"=>"a", "Č"=>"c", "Ē"=>"e",
		          "Ģ"=>"g", "Ī"=>"i", "Ķ"=>"k", "Ļ"=>"l",
		          "Ņ"=>"n", "Š"=>"s", "Ū"=>"u", "Ž"=>"z",
        			"ā"=>"a", "č"=>"c", "ē"=>"e", "ģ"=>"g",
        			"ī"=>"i", "ķ"=>"k", "ļ"=>"l", "ņ"=>"n",
        			"š"=>"s", "ū"=>"u", "ž"=>"z",	"Ą"=>"a",
        			"Ć"=>"c", "Ę"=>"e", "Ł"=>"l", "Ń"=>"n",
							"Ó"=>"o", "Ś"=>"s", "Ź"=>"z", "Ż"=>"z",
							"ą"=>"a", "ć"=>"c", "ę"=>"e", "ł"=>"l",
							"ń"=>"n", "ó"=>"o", "ś"=>"s", "ź"=>"z",
							"ż"=>"z", "Ä"=>"a", "Ö"=>"o", "ẞ"=>"s",
							"Ü"=>"u", "ä"=>"a", "ö"=>"o", "ß"=>"s",
							"ü"=>"u"
  					];

	$text = strtr($text, $symbol);
	return $text;
}

//Фунция кодирования ссылок на php
public function urlEncoding($url){
	#$restored2 = $url;
	$restored = $url;
	
		$url = parse_url(rawurldecode(trim($url)));

    if(empty($url['scheme'])) { $url['scheme'] = '';} else { $url['scheme'] = $url['scheme'].'://';}

    //преобразования доменных имен в ascii
    if(empty($url['host'])) { 
    	
    	$url['host'] = '';
  	
  	} else { 
  		
  		//проверяем есть ли такая фунция на хостинге. Если есть юзаем ее.
  		if (function_exists('idn_to_ascii')) {
  			@$temp_host = idn_to_ascii($url['host']);
  			if(!empty($temp_host)){ $url['host'] = $temp_host;}	
  		}
  	}

    if(empty($url['path'])) { $url['path'] = '';}
    if(empty($url['query'])) { $url['query'] = '';} else { $url['query'] = '?'.$url['query'];}

    $path = explode('/', $url['path']);        
    $path = array_map('rawurlencode', $path);                                    
    #$query = explode('&', $url['query']);        
    #$query = array_map('rawurlencode', $query);                                  // Обработать массив функцией rawurlencode
    @$restored = $url['scheme'].$url['host'].implode('/', $path).$url['query'];   // Собрать перекодированный url обратно
    $restored = str_replace('%3D', '=', $restored); // Ибо rawurlencode заменяет равенство '=' на '%3D'
    # $restored = str_replace('%23', '#', $$restored); // Ибо rawurlencode заменяет якорь '#' на ''%23'
    #$this->wtfarrey($restored);
	
  #$this->wtfarrey($restored);
  #$restored = $restored2;
  return $restored;
}

//Версия модуля.
public function simpleParsVersion(){
	return 'v3.7-7';
}

public function wtfarrey($data){
	#echo '<pre>';
	#print_r($data);
	#echo '</pre>';
}

}

?>
