<?php
class ModelExtensionModulePreOrder extends Model {
	public function addPreOrder($data) {
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		
		$this->load->model('catalog/product');
						
		$product = $this->model_catalog_product->getProduct($data['product_id']);
		
		if ($product) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "preorder SET email = '" . $this->db->escape($data['email']) . "', phone = '" . $this->db->escape($data['phone']) . "', product_id = '" . (int)$data['product_id'] . "', product_name = '" . $this->db->escape($product['name']) . "', product_option = '" . $this->db->escape($data['option']) . "', store_name = '" . $this->db->escape($this->config->get('config_name')) . "', store_url = '" . $server . "', status = '0', date_added = NOW()");
			
			
			
			$preorder_id = $this->db->getLastId();
			
			// E-mail
			if ($this->config->get('module_preorder_mail_alert')) {
				
				$this->load->language('mail/preorder');
				
				$this->load->model('catalog/product');
				
				$preorder = $this->getPreOrder($preorder_id); 
				
				$product_options = array();
				$text_option = '';
				
				foreach ($this->model_catalog_product->getProductOptions($preorder['product_id']) as $product_option) {
					
					$product_option_value_data = array();
					
					foreach ($product_option['product_option_value'] as $product_option_value) {
						
						$option_value_info = $this->getOptionValue($product_option_value['option_value_id']);
						
						$product_option_value_data[] = array(
							'product_option_value_id' => $product_option_value['product_option_value_id'],
							'name'                    => $option_value_info['name'],
						);
					}
					
					$product_options[] = array(
						'product_option_id'    => $product_option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'name'                 => $product_option['name']
					);
				}
				
				$subject = sprintf($this->language->get('text_subject'), $preorder['store_name']);
				
				$text  = $subject . "\n\n";
				$text .= sprintf($this->language->get('text_header'), $preorder['product_name']) . "\n";
				
				if ($preorder['product_option']) {
					foreach ($preorder['product_option'] as $key => $option) {
						if (strpbrk($option, ',')) {
							$preorder['product_option'][$key] = explode(',', $option);
						}
					}
					
					$text_option .= "\n" . $this->language->get('text_option') . "\n";
				}
							
				foreach ($product_options as $product_option) {
					if (isset($preorder['product_option'][$product_option['product_option_id']])) {
						$text_option .= "\n" . $product_option['name'] . ": ";
						$i = 0;
						
						foreach ($product_option['product_option_value'] as $product_option_value) {
							if (!is_array($preorder['product_option'][$product_option['product_option_id']])) {
								if ($preorder['product_option'][$product_option['product_option_id']] == $product_option_value['product_option_value_id']) {
									$text_option .=  ($i ? ', ' : '') . $product_option_value['name'];
									$i++;
								}
							} else {
								foreach ($preorder['product_option'][$product_option['product_option_id']] as $option_value) {
									if ($option_value == $product_option_value['product_option_value_id']) {
										$text_option .=  ($i ? ', ' : '') . $product_option_value['name']; 
                                        $i++;										
									}
								}					
							}
						}
					}
				}
				
				$text .= $text_option ? $text_option . "\n" : "";
				$text .= "\n" . $this->language->get('text_link') . ": " . $preorder['store_url'] . "index.php?route=product/product&product_id=" . $preorder['product_id'] . "\n\n";
				
				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($this->config->get('config_email'));
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($preorder['store_name'], ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
				$mail->send();
				
				// Send to additional alert emails
				$emails = explode(',', $this->config->get('config_alert_email'));
		
				foreach ($emails as $email) {
					if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$mail->setTo($email);
						$mail->send();
					}
				}
			}
		}
	}
	
	public function getPreOrder($preorder_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "preorder WHERE preorder_id = '" . (int)$preorder_id . "'");
		
		if ($query->num_rows) {
			return array(
				'preorder_id'      => $query->row['preorder_id'],
				'product_id'       => $query->row['product_id'],
				'product_name'     => $query->row['product_name'],
				'product_option'   => unserialize($query->row['product_option']),
				'email'      	   => $query->row['email'],
				'phone'      	   => $query->row['phone'],
				'status'      	   => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'store_name'       => $query->row['store_name'],
				'store_url'        => $query->row['store_url']
			);
		} else {
			return false;
		}
	}
	
	public function getOptionValue($option_value_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_value_id = '" . (int)$option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
}