<?php

/**
* Класс рисования ссылок
*/
class LLM_client extends LLM_base {

	/*<LLM_CONFIG>*/
		    var $_llm_main_host     = 'mediaindustrial.ru';
		    var $_llm_main_uri      = 'llm-client';
		    var $_llm_is_ftp        = false;
		    var $_llm_method_simple = false;
			/*</LLM_CONFIG>*/
    
    // [file_get_contents|curl|socket]
    var $_fetch_remote_type = '';
    
    //сделайте true если ничего не работает
    var $_debug             = false;
    var $_debug_msg         = "";

    /**
    * Тут возможны ошибки - инициализируемся через родителя
    */
    function LLM_client() {

    	$this->_links_page      = array();
		$this->_links_delimiter = "; ";
		$this->_css_class       = "";
		$this->_static_code     = array();
		
    	parent::LLM_base();
        $this->load_data();
    }

    /**
    * Отдавалка ссылок
    */
    function return_links($n = null, $offset = 0) {

    	//проверка при индексации
    	$llm_xxx = isset($_COOKIE['llm_xxx']) ? $_COOKIE['llm_xxx'] : '';
    	$is_indexing = $llm_xxx == _LLM_DOMAIN_KEY ? true : false;
    	if ($is_indexing || ( isset($_REQUEST['domain_key']) && $_REQUEST['domain_key']==_LLM_DOMAIN_KEY )) {
    	
    		return "<!-- ".md5('llm_xxx='.$llm_xxx)." --> LLM";
    	}
    	
        if (is_array($this->_links_page)) {

            $total_page_links = count($this->_links_page);

            if (!is_numeric($n) || $n > $total_page_links) {
                $n = $total_page_links;
            }

            $links = array();

            for ($i = 1; $i <= $n; $i++) {
                if ($offset > 0 && $i <= $offset) {
                    array_shift($this->_links_page);
                } else {
                    $links[] = array_shift($this->_links_page);
                }
            }

            $html = join($this->_links_delimiter, $links);
            if ($this->_css_class) $html = preg_replace("|<a[ ]+href|i","<a class='{$this->_css_class}' href",$html);
            
            return $html;
        }
    }
    
    /**
    * Возвращает весь код, содержащийся на заданной позиции
    */
    function return_static($position_name) {
    
    	if (isset($this->_static_code[$position_name])) return implode($this->_static_code[$position_name]);
    }
}

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

class LLM_base {

    var $_version           = '1';
    var $_cache_lifetime    = 43200;//3600*12 - то есть 12 часов
	var $_cache_reloadtime  = 600;
	
    function LLM_base() {
    
        $this->_host        = $_SERVER['HTTP_HOST'];
        $this->_request_uri = $_SERVER['REQUEST_URI'];
    }

    /**
    * Рисовалка и скрывалка ошибок
    */
    function showError($msg) {
    
    	if ($this->_debug) {
    	
    		echo "[[{$msg}]]";
    	}
    	else {
    	
    		echo "[error]";
    	}
    }
    
    /**
    * Функция чтения файла по сети, содрал с сапы.
    */
    function get_file($host, $path) {

        @ini_set('allow_url_fopen',          1);
        @ini_set('default_socket_timeout',   6);
			
        if (
            $this->_fetch_remote_type == 'file_get_contents'
            ||
            (
                $this->_fetch_remote_type == ''
                &&
                function_exists('file_get_contents')
                &&
                ini_get('allow_url_fopen') == 1
            )
        ) {
			$this->_fetch_remote_type = 'file_get_contents';
            if ($data = @file_get_contents('http://' . $host . $path)) {
                return $data;
            }

        } elseif (
            $this->_fetch_remote_type == 'curl'
            ||
            (
                $this->_fetch_remote_type == ''
                &&
                function_exists('curl_init')
            )
        ) {
        	
			$this->_fetch_remote_type = 'curl';
            if ($ch = @curl_init()) {
                @curl_setopt($ch, CURLOPT_URL,              'http://' . $host . $path);
                @curl_setopt($ch, CURLOPT_HEADER,           false);
                @curl_setopt($ch, CURLOPT_RETURNTRANSFER,   true);
                @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,   $this->_socket_timeout);
                @curl_setopt($ch, CURLOPT_USERAGENT,        $user_agent);

                if ($data = curl_exec($ch)) {
                	
                    return $data;
                }
                
                @curl_close($ch);
            }

        } else {
			$this->_fetch_remote_type = 'socket';
            $buff = '';
            $fp = @fsockopen($host, 80, $errno, $errstr, $this->_socket_timeout);
            if ($fp) {
                @fputs($fp, "GET {$path} HTTP/1.0\r\nHost: {$host}\r\n");
                @fputs($fp, "User-Agent: XpEHb-B-TArIo4KAX\r\n\r\n");
                while (!@feof($fp)) {
                    $buff .= @fgets($fp, 128);
                }
                @fclose($fp);

                $page = explode("\r\n\r\n", $buff);

                return $page[1];
            }

        }

        return null;
    }

    /*
     * Функция чтения из локального файла
     */
    function _read($filename) {

        $fp = @fopen($filename, 'rb');
        @flock($fp, LOCK_SH);
        if ($fp) {
            clearstatcache();
            $length = @filesize($filename);
            $mqr = get_magic_quotes_runtime();            
            ini_set("magic_quotes_runtime", 0);
            if ($length) {
                $data = @fread($fp, $length);
            } else {
                $data = '';
            }
            
            ini_set("magic_quotes_runtime", $mqr);
            @flock($fp, LOCK_UN);
            @fclose($fp);

            return $data;
        }

        return null;
    }

    /*
     * Функция записи в локальный файл
     */
    function _write($filename, $data) {

        $fp = @fopen($filename, 'wb');
        if ($fp) {
            @flock($fp, LOCK_EX);
            $length = strlen($data);
            @fwrite($fp, $data, $length);
            @flock($fp, LOCK_UN);
            @fclose($fp);

            if (md5($this->_read($filename)) != md5($data)) {
                return null;
            }

            return true;
        }

        return null;
    }    

    /**
    * Грузим что-то там
    */
    function load_data() {
    	
    	//где храним ссылки
        $this->_db_file = dirname(__FILE__)."/llm-links.txt";
        
        // Пытаемся создать файл.
        if (!is_file($this->_db_file)) {
            if (@touch($this->_db_file)) {
                @chmod($this->_db_file, 0666);
            } else {
            	
            	$this->showError("Cannot touch {$this->_db_file}");
                return null;
            }
        }
        
		//можем ли до сих пор в него писать
        if (!is_writable($this->_db_file)) {
        	$this->showError("{$this->_db_file} is unwriteable");
            return null;
        }

        @clearstatcache();

        //попытка не умирать если нет соединения с донором
        $is_error = false;
        
        //а тут загружаем с нашего сервера ссылки для этого домена
        if (!$this->_llm_is_ftp && filemtime($this->_db_file) < (time()-$this->_cache_lifetime) || filesize($this->_db_file) == 0) {

            @touch($this->_db_file, (time() - $this->_cache_lifetime + $this->_cache_reloadtime));

            if (true) {//!$this->_llm_method_simple
            	
	            //REQUEST_URI запроса ссылок к нашему крутому серверу, раздаюущему ссылки для конкретной страницы
	            $req_uri = "/".$this->_llm_main_uri.'/get.php?method=secure&send_magic=1&domain_key='._LLM_DOMAIN_KEY.'&send_static_code=1';
            }
            else {
            
            	$req_uri = "/".$this->_llm_main_uri.'/get.php?method=simple&send_magic=1&domain='.$_SERVER['HTTP_HOST'].'&send_static_code=1';
            }
            $data = $this->get_file($this->_llm_main_host,$req_uri);
			
            if ($data === null) {
            	$this->showError("NULL answer from llm-client");
            	
            	$is_error = true;
            }
            
            //ловим LERROR:
            $pos = strpos($data,"LERROR:");
            
            if ($pos !== false && !$is_erorr) {
            	$this->showError("llm-client send error [$data]");
            	
            	$is_error = true;
            }

            //правильно ли отвечает хостинг
        	$pos = strpos($data,"LLM_MAGIC_CONSTANT");
        	if ($pos===false) {
        	
        		//строка не найдена - значит это большая ошибка доступа к хосту
        		$this->showError("llm-client magic constant not found");
        		$is_error = true;
        		return NULL;
        	}
        	else {
        	
        		//иначе просто убираем и смело декодируем строку
        		$data = str_replace("LLM_MAGIC_CONSTANT","",$data);
        	}
        	
            //если ошибок нет, то записываем файл
            $ret = !$is_error ? $this->_write($this->_db_file,base64_decode($data)) : NULL;
            if ($ret === null && !$is_error) {
            
            	$this->showError("Write function return NULL");
            	$is_error = true;
            }
        }

        if (strlen(session_id())) {
            $session = session_name() . '=' . session_id();
            $this->_request_uri = str_replace(array('?'.$session,'&'.$session), '', $this->_request_uri);
        }

        //считываем файл и в функции преобразуем в формат для непосредственной вставки
        if ($data = $this->_read($this->_db_file)) {
        	
        	//если была ошибка и в файле ничего нет, то уходим, тут точно ничего нет
        	if ($is_error && !$data) return NULL;
        	
        	$pos = strpos($data,"LLM_STATIC_CODE_DELIMITER");
        	if ($pos !== false) {
        	
        		//значит посылался какой-то статичный код и надо его в отдельную переменную запихать
        		list($data,$sdata) = explode("LLM_STATIC_CODE_DELIMITER",$data);
        	}
       	
        	$ret3 = $this->set_data(@unserialize($data));
        	if (!$ret3) {
        	
        		$this->showError("Links file is not an array");
        		return null;
        	}
        	//и данные статичных блоков
        	$ret4 = $this->set_sdata(@unserialize($sdata));
        	if (!$ret4) {
        	
        		$this->showError("Links file is not an array [2]");
        		return null;
        	}
        }
        else {
        
        	$this->showError("Cannot read {$this->_db_file}");
        	return null;
        }
        
        //если все хорошо, то уходим
        return true;
    }
    
    /**
    * Парсим файл со ссылками в удобоваримый вид.
    */
    function set_data($data){

    	if (!is_array($data)) return false;
    	
    	foreach($data as $ind => $link) {
    	
    		$full_url = $link->pages_url == '/' ? $link->sites_url : $link->sites_url.$link->pages_url;
    		$pu = parse_url($full_url);
    		
    		//составляем request_uri
    		$path  = isset($pu['path']) ? $pu['path'] : '';
    		$path .= isset($pu['query']) ? "?".$pu['query'] : '';
    		
    		if ($path == $this->_request_uri) $this->_links_page[] = $link->html;
    		$this->_links_delimiter = $link->links_delimiter;
    		$this->_css_class       = $link->css_class;
    	}
    	
    	return true;
    }
    
    /**
    * Установка данных статичных кодов
    */
    function set_sdata($sdata) {
    
    	if (!is_array($sdata)) return false;
    	
    	foreach($sdata as $code) {
    	
    		$position_name = $code->position_name;
    		if ($code->is_published) $this->_static_code[$position_name][] = $code->content;
    	}
    	
    	return true;
    }
}
?>