<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
define('PDO_DEBUG', true);

class DB {
	protected $pdo;
	protected $cfg;
	protected $tablepre;
	protected $result;
	protected $statement;
	protected $errors = array();
	protected $link = array();

	public function getPDO() {
		return $this->pdo;
	}

	public function __construct($name = 'master') {
		global $_W;
		$this->cfg = $_W['config']['db'];
		$this->connect($name);
	}

	public function connect($name = 'master') {
		if(is_array($name)) {
			$cfg = $name;
		} else {
			$cfg = $this->cfg[$name];
		}
		$this->tablepre = $cfg['tablepre'];
		if(empty($cfg)) {
			exit("The master database is not found, Please checking 'data/config.php'");
		}
		$dsn = "mysql:dbname={$cfg['database']};host={$cfg['host']};port={$cfg['port']};charset={$cfg['charset']}";
		$dbclass = '';
		$options = array();
		if (class_exists('PDO')) {
			if (extension_loaded("pdo_mysql") && in_array('mysql', PDO::getAvailableDrivers())) {
				$dbclass = 'PDO';
				$options = array(PDO::ATTR_PERSISTENT => $cfg['pconnect']);
			} else {
				if(!class_exists('_PDO')) {
					include IA_ROOT . '/framework/library/pdo/PDO.class.php';
				}
				$dbclass = '_PDO';
			}
		} else {
			include IA_ROOT . '/framework/library/pdo/PDO.class.php';
			$dbclass = 'PDO';
		}
		$this->pdo = new $dbclass($dsn, $cfg['username'], $cfg['password'], $options);
		//$this->pdo->setAttribute(pdo::ATTR_EMULATE_PREPARES, false);
		
		$sql = "SET NAMES '{$cfg['charset']}';";
		$this->pdo->exec($sql);
		$this->pdo->exec("SET sql_mode='';");
		if(is_string($name)) {
			$this->link[$name] = $this->pdo;
		}
		if(PDO_DEBUG) {
			$info = array();
			$info['sql'] = $sql;
			$info['error'] = $this->pdo->errorInfo();
			$this->debug(false, $info);
		}
	}

	public function prepare($sql) {
		$sqlsafe = SqlChecker::checkquery($sql);
		if (is_error($sqlsafe)) {
			trigger_error($sqlsafe['message'], E_USER_ERROR);
			return false;
		}
		$statement = $this->pdo->prepare($sql);
		return $statement;
	}
	
	
	public function query($sql, $params = array()) {

        $sqlsafe = SqlChecker::checkquery($sql);
		if (is_error($sqlsafe)) {
			trigger_error($sqlsafe['message'], E_USER_ERROR);
			return false;
		}
				if (in_array(strtolower(substr($sql, 0, 6)), array('update', 'delete', 'insert', 'replac'))) {
			$this->cacheNameSpace($sql, true);
		}


        /*foreach ($params as $key=>$val){
            $arr1[] = $key;
            $arr2[] = '\''.$val.'\'';
        }
        $sql2 = str_replace($arr1,$arr2,$sql);
        WeUtility::logging('TODO debug3',  array('file'=>'D:\www\users\wd2.jieqiangtec.com\framework\class\db.class.php query($sql, $params = array()) ','sql2'=>$sql2,'$params'=>$params));*/


		$starttime = microtime();
		if (empty($params)) {
			$result = $this->pdo->exec($sql);
			if(PDO_DEBUG) {
				$info = array();
				$info['sql'] = $sql;
				$info['error'] = $this->pdo->errorInfo();
				$this->debug(false, $info);
			}
			return $result;
		}
		$statement = $this->prepare($sql);
		$result = $statement->execute($params);
		if(PDO_DEBUG) {
			$info = array();
			$info['sql'] = $sql;
			$info['params'] = $params;
			$info['error'] = $statement->errorInfo();
			$this->debug(false, $info);
		}
		$endtime = microtime();
		$this->performance($sql, $endtime - $starttime);
		if (!$result) {
			return false;
		} else {
			return $statement->rowCount();
		}
	}

	
	public function fetchcolumn($sql, $params = array(), $column = 0) {
		$cachekey = $this->cacheKey($sql, $params);
		if (($cache = $this->cacheRead($cachekey)) !== false) {
			return $cache['data'];
		}
		$starttime = microtime();
		$statement = $this->prepare($sql);
		$result = $statement->execute($params);
		if(PDO_DEBUG) {
			$info = array();
			$info['sql'] = $sql;
			$info['params'] = $params;
			$info['error'] = $statement->errorInfo();
			$this->debug(false, $info);
		}
		$endtime = microtime();
		$this->performance($sql, $endtime - $starttime);
		if (!$result) {
			return false;
		} else {
			$data = $statement->fetchColumn($column);
			$this->cacheWrite($cachekey, $data);
			return $data;
		}
	}
	
	
	public function fetch($sql, $params = array()) {
		$cachekey = $this->cacheKey($sql, $params);
		if (($cache = $this->cacheRead($cachekey)) !== false) {
			return $cache['data'];
		}
		$starttime = microtime();
		$statement = $this->prepare($sql);
		$result = $statement->execute($params);

        /*foreach ($params as $key=>$val){
            $arr1[] = $key;
            $arr2[] = '\''.$val.'\'';
        }
        $sql2 = str_replace($arr1,$arr2,$sql);
        WeUtility::logging('TODO debug2',  array('file'=>'D:\www\users\wd2.jieqiangtec.com\framework\class\db.class.php fetch($sql, $params = array()) ','sql2'=>$sql2,'$params'=>$params));
//        WeUtility::logging('TODO debug2',  array('file'=>'D:\www\users\wd2.jieqiangtec.com\framework\class\db.class.php fetch($sql, $params = array()) ','sql2'=>$sql2,'sql'=>$sql,'$params'=>$params));*/


        if(PDO_DEBUG) {
			$info = array();
			$info['sql'] = $sql;
			$info['params'] = $params;
			$info['error'] = $statement->errorInfo();
			$this->debug(false, $info);
		}
		$endtime = microtime();
		$this->performance($sql, $endtime - $starttime);
		if (!$result) {
			return false;
		} else {
			$data = $statement->fetch(pdo::FETCH_ASSOC);
			$this->cacheWrite($cachekey, $data);
			return $data;
		}
	}

	
	public function fetchall($sql, $params = array(), $keyfield = '') {
		$cachekey = $this->cacheKey($sql, $params);
		if (($cache = $this->cacheRead($cachekey)) !== false) {
			return $cache['data'];
		}
		$starttime = microtime();
		$statement = $this->prepare($sql);
		$result = $statement->execute($params);
		if(PDO_DEBUG) {
			$info = array();
			$info['sql'] = $sql;
			$info['params'] = $params;
			$info['error'] = $statement->errorInfo();
			$this->debug(false, $info);
		}
		$endtime = microtime();
		$this->performance($sql, $endtime - $starttime);
		if (!$result) {
			return false;
		} else {
			if (empty($keyfield)) {
				$result = $statement->fetchAll(pdo::FETCH_ASSOC);
			} else {
				$temp = $statement->fetchAll(pdo::FETCH_ASSOC);
				$result = array();
				if (!empty($temp)) {
					foreach ($temp as $key => &$row) {
						if (isset($row[$keyfield])) {
							$result[$row[$keyfield]] = $row;
						} else {
							$result[] = $row;
						}
					}
				}
			}
			$this->cacheWrite($cachekey, $result);
			return $result;
		}
	}
	
	public function get($tablename, $params = array(), $fields = array()) {
		$select = $this->parseSelect($fields);
		$condition = $this->implode($params, 'AND');
		$sql = "SELECT {$select} FROM " . $this->tablename($tablename) . (!empty($condition['fields']) ? " WHERE {$condition['fields']}" : '') . " LIMIT 1";
		return $this->fetch($sql, $condition['params']);
	}
	
	public function getall($tablename, $params = array(), $fields = array(), $keyfield = '', $orderby = array(), $limit = array()) {
		$select = $this->parseSelect($fields);
		$condition = $this->implode($params, 'AND');
		$limitsql = '';
		
		if (!empty($limit)) {
			if (is_array($limit)) {
				if (count($limit) == 1) {
					$limitsql = " LIMIT " . $limit[0];
				} else {
					$limitsql = " LIMIT " . ($limit[0] - 1) * $limit[1] . ', ' . $limit[1];
				}
			} else {
				$limitsql = strexists(strtoupper($limit), 'LIMIT') ? " $limit " : " LIMIT $limit";
			}
		}
		
		if (!empty($orderby)) {
			if (is_array($orderby)) {
				$orderbysql = implode(',', $orderby);
			} else {
				$orderbysql = $orderby;
			}
		}
		
		$sql = "SELECT {$select} FROM " .$this->tablename($tablename) . (!empty($condition['fields']) ? " WHERE {$condition['fields']}" : '') . (!empty($orderbysql) ? " ORDER BY $orderbysql " : '') . $limitsql;
		return $this->fetchall($sql, $condition['params'], $keyfield);
	}
	
	public function getslice($tablename, $params = array(), $limit = array(), &$total = null, $fields = array(), $keyfield = '', $orderby = array()) {
		$select = $this->parseSelect($fields);
		$condition = $this->implode($params, 'AND');
		if (!empty($limit)) {
			if (is_array($limit)) {
				$limitsql = " LIMIT " . ($limit[0] - 1) * $limit[1] . ', ' . $limit[1];
			} else {
				$limitsql = strexists(strtoupper($limit), 'LIMIT') ? " $limit " : " LIMIT $limit";
			}
		}
		
		if (!empty($orderby)) {
			if (is_array($orderby)) {
				$orderbysql = implode(',', $orderby);
			} else {
				$orderbysql = $orderby;
			}
		}
		$sql = "SELECT {$select} FROM " . $this->tablename($tablename) . (!empty($condition['fields']) ? " WHERE {$condition['fields']}" : '') . (!empty($orderbysql) ? " ORDER BY $orderbysql " : '') . $limitsql;
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($tablename) . (!empty($condition['fields']) ? " WHERE {$condition['fields']}" : ''), $condition['params']);
		return $this->fetchall($sql, $condition['params'], $keyfield);
	}
	
	public function getcolumn($tablename, $params = array(), $field = '') {
		$result = $this->get($tablename, $params, $field);
		if (!empty($result)) {
			if (strexists($field, '(')) {
				return array_shift($result);
			} else {
				return $result[$field];
			}
		} else {
			return false;
		}
	}

	
	public function update($table, $data = array(), $params = array(), $glue = 'AND') {
		$fields = $this->implode($data, ',');
		$condition = $this->implode($params, $glue);
		$params = array_merge($fields['params'], $condition['params']);
		$sql = "UPDATE " . $this->tablename($table) . " SET {$fields['fields']}";
		$sql .= $condition['fields'] ? ' WHERE '.$condition['fields'] : '';
		return $this->query($sql, $params);
	}

	
	public function insert($table, $data = array(), $replace = FALSE) {
		$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
		$condition = $this->implode($data, ',');
		return $this->query("$cmd " . $this->tablename($table) . " SET {$condition['fields']}", $condition['params']);
	}
	
	
	public function insertid() {
		return $this->pdo->lastInsertId();
	}

	
	public function delete($table, $params = array(), $glue = 'AND') {
		$condition = $this->implode($params, $glue);
		$sql = "DELETE FROM " . $this->tablename($table);
		$sql .= $condition['fields'] ? ' WHERE '.$condition['fields'] : '';
		return $this->query($sql, $condition['params']);
	}

	
	public function begin() {
		$this->pdo->beginTransaction();
	}

	
	public function commit() {
		$this->pdo->commit();
	}

	
	public function rollback() {
		$this->pdo->rollBack();
	}

	
	private function implode($params, $glue = ',') {
		$result = array('fields' => ' 1 ', 'params' => array());
		$split = '';
		$suffix = '';
		$allow_operator = array('>', '<', '<>', '!=', '>=', '<=', '+=', '-=', 'LIKE', 'like');
		if (in_array(strtolower($glue), array('and', 'or'))) {
			$suffix = '__';
		}
		if (!is_array($params)) {
			$result['fields'] = $params;
			return $result;
		}
		if (is_array($params)) {
			$result['fields'] = '';
			foreach ($params as $fields => $value) {
				$operator = '';
				if (strpos($fields, ' ') !== FALSE) {
					list($fields, $operator) = explode(' ', $fields, 2);
					if (!in_array($operator, $allow_operator)) {
						$operator = '';
					}
				}
				if (empty($operator)) {
					$fields = trim($fields);
					if (is_array($value) && !empty($value)) {
						$operator = 'IN';
					} else {
						$operator = '=';
					}
				} elseif ($operator == '+=') {
					$operator = " = `$fields` + ";
				} elseif ($operator == '-=') {
					$operator = " = `$fields` - ";
				} elseif ($operator == '!=' || $operator == '<>') {
										if (is_array($value) && !empty($value)) {
						$operator = 'NOT IN';
					}
				}
				if (is_array($value) && !empty($value)) {
					$insql = array();
										$value = array_values($value);
					foreach ($value as $k => $v) {
						$insql[] = ":{$suffix}{$fields}_{$k}";
						$result['params'][":{$suffix}{$fields}_{$k}"] = is_null($v) ? '' : $v;
					}
					$result['fields'] .= $split . "`$fields` {$operator} (".implode(",", $insql).")";
					$split = ' ' . $glue . ' ';
				} else {
					$result['fields'] .= $split . "`$fields` {$operator}  :{$suffix}$fields";
					$split = ' ' . $glue . ' ';
					$result['params'][":{$suffix}$fields"] = is_null($value) || is_array($value) ? '' : $value;
				}
			}
		}
		return $result;
	}
	
	private function parseSelect($field = array()) {
		if (empty($field)) {
			return '*';
		}
		if (!is_array($field)) {
			$field = array($field);
		}
		$select = array();
		$index = 0;
		foreach ($field as $field_row) {
			if (strexists($field_row, '*')) {
				if (!strexists(strtolower($field_row), 'as')) {
					$field_row .= " AS '{$index}'";
				}
			} elseif (strexists(strtolower($field_row), 'select')) {
								if ($field_row[0] != '(') {
					$field_row = "($field_row) AS '{$index}'";
				}
			} elseif (strexists($field_row, '(')) {
				$field_row = str_replace(array('(', ')'), array('(`',  '`)'), $field_row);
								if (!strexists(strtolower($field_row), 'as')) {
					$field_row .= " AS '{$index}'";
				}
			} else {
				$field_row = '`'. $field_row. '`';
			}
			$select[] = $field_row;
			$index++;
		}
		return implode(',', $select);
	}
	
	
	public function run($sql, $stuff = 'ims_') {
		if(!isset($sql) || empty($sql)) return;

		$sql = str_replace("\r", "\n", str_replace(' ' . $stuff, ' ' . $this->tablepre, $sql));
		$sql = str_replace("\r", "\n", str_replace(' `' . $stuff, ' `' . $this->tablepre, $sql));
		$ret = array();
		$num = 0;
		$sql = preg_replace("/\;[ \f\t\v]+/", ';', $sql);
		foreach(explode(";\n", trim($sql)) as $query) {
			$ret[$num] = '';
			$queries = explode("\n", trim($query));
			foreach($queries as $query) {
				$ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
			}
			$num++;
		}
		unset($sql);
		foreach($ret as $query) {
			$query = trim($query);
			if($query) {
				$this->query($query, array());
			}
		}
	}
	
	
	public function fieldexists($tablename, $fieldname) {
		$isexists = $this->fetch("DESCRIBE " . $this->tablename($tablename) . " `{$fieldname}`", array());
		return !empty($isexists) ? true : false;
	}

	
	public function fieldmatch($tablename, $fieldname, $datatype = '', $length = '') {
		$datatype = strtolower($datatype);
		$field_info = $this->fetch("DESCRIBE " . $this->tablename($tablename) . " `{$fieldname}`", array());
		if (empty($field_info)) {
			return false;
		}
		if (!empty($datatype)) {
			$find = strexists($field_info['Type'], '(');
			if (empty($find)) {
				$length = '';
			}
			if (!empty($length)) {
				$datatype .= ("({$length})");
			}
			return strpos($field_info['Type'], $datatype) === 0 ? true : -1;
		}
		return true;
	}

	
	public function indexexists($tablename, $indexname) {
		if (!empty($indexname)) {
			$indexs = $this->fetchall("SHOW INDEX FROM " . $this->tablename($tablename), array(), '');
			if (!empty($indexs) && is_array($indexs)) {
				foreach ($indexs as $row) {
					if ($row['Key_name'] == $indexname) {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	
	public function tablename($table) {
		return "`{$this->tablepre}{$table}`";
	}

	
	public function debug($output = true, $append = array()) {
		if(!empty($append)) {
			$output = false;
			array_push($this->errors, $append);
		}
		if($output) {
			print_r($this->errors);
		} else {
			if (!empty($append['error'][1])) {
				$traces = debug_backtrace();
				$ts = '';
				foreach($traces as $trace) {
					$trace['file'] = str_replace('\\', '/', $trace['file']);
					$trace['file'] = str_replace(IA_ROOT, '', $trace['file']);
					$ts .= "file: {$trace['file']}; line: {$trace['line']}; <br />";
				}
				$params = var_export($append['params'], true);
				if (!function_exists('message')) {
					load()->web('common');
					load()->web('template');
				}
				WeUtility::logging('SQL Error', "SQL: <br/>{$append['sql']}<hr/>Params: <br/>{$params}<hr/>SQL Error: <br/>{$append['error'][2]}<hr/>Traces: <br/>{$ts}");
				trigger_error("SQL: <br/>{$append['sql']}<hr/>Params: <br/>{$params}<hr/>SQL Error: <br/>{$append['error'][2]}<hr/>Traces: <br/>{$ts}", E_USER_WARNING);
			}
		}
		return $this->errors;
	}

	
	public function tableexists($table) {
		if(!empty($table)) {
			$data = $this->fetch("SHOW TABLES LIKE '{$this->tablepre}{$table}'", array());
			if(!empty($data)) {
				$data = array_values($data);
				$tablename = $this->tablepre . $table;
				if(in_array($tablename, $data)) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	private function performance($sql, $runtime = 0) {
		global $_W;
		if ($runtime == 0) {
			return false;
		}
		if (strexists($sql, 'core_performance')) {
			return false;
		}
				if (empty($_W['config']['setting']['maxtimesql'])) {
			$_W['config']['setting']['maxtimesql'] = 5;
		}
		if ($runtime > $_W['config']['setting']['maxtimesql']) {
			$sqldata = array(
				'type' => '2',
				'runtime' => $runtime,
				'runurl' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
				'runsql' => $sql,
				'createtime' => time()
			);
			$this->insert('core_performance', $sqldata);
		}
		return true;
	}
	
	private function cacheRead($cachekey) {
		global $_W;
		if (empty($cachekey) || $_W['config']['setting']['cache'] != 'memcache' || empty($_W['config']['setting']['memcache']['sql'])) {
			return false;
		}
		$data = cache_read($cachekey, true);
		if (empty($data) || empty($data['data'])) {
			return false;
		}
		return $data;
	}
	
	private function cacheWrite($cachekey, $data) {
		global $_W;
		if (empty($data) || empty($cachekey) || $_W['config']['setting']['cache'] != 'memcache' || empty($_W['config']['setting']['memcache']['sql'])) {
			return false;
		}
		$cachedata = array(
			'data' => $data,
			'expire' => TIMESTAMP + 2592000,
		);
		cache_write($cachekey, $cachedata, 0, true);
		return true;
	}
	
	private function cacheKey($sql, $params) {
		global $_W;
		if ($_W['config']['setting']['cache'] != 'memcache' || empty($_W['config']['setting']['memcache']['sql'])) {
			return false;
		}
		$namespace = $this->cacheNameSpace($sql);
		if (empty($namespace)) {
			return false;
		}
		return $namespace . ':' . md5($sql . serialize($params));
	}
	
	
	private function cacheNameSpace($sql, $forcenew = false) {
		global $_W;
		if ($_W['config']['setting']['cache'] != 'memcache' || empty($_W['config']['setting']['memcache']['sql'])) {
			return false;
		}
		$skip_tablename = array(
			$this->tablename('core_cache'),
			$this->tablename('core_queue'),
			$this->tablename('mc_member'),
			$this->tablename('mc_mapping_fans'),
		);
				$table_prefix = str_replace('`', '', tablename(''));
		preg_match_all('/(?!from|insert into|replace into|update) `?('.$table_prefix.'[a-zA-Z0-9_-]+)/i', $sql, $match);
		$tablename = implode(':', $match[1]);
		if (empty($tablename) || in_array("`{$tablename}`", $skip_tablename)) {
			return false;
		}
		$tablename = str_replace($this->tablepre, '', $tablename);
				$db_cache_key = 'we7:dbkey:'.$tablename;
		$namespace = $this->getColumn('core_cache', array('key' => $db_cache_key), 'value');
		if (empty($namespace) || $forcenew) {
			$namespace = random(8);
			$this->delete('core_cache', array('key LIKE' => "%{$tablename}%", 'key !=' => $db_cache_key));
			$this->insert('core_cache', array('key' => $db_cache_key, 'value' => $namespace), true);
		}
		return $tablename . ':' . $namespace;
	}
}


class SqlChecker {
	private static $checkcmd = array('SELECT', 'UPDATE', 'INSERT', 'REPLAC', 'DELETE');
	private static $disable = array(
		'function' => array('load_file', 'floor', 'hex', 'substring', 'if', 'ord', 'char', 'benchmark', 'reverse', 'strcmp', 'datadir', 'updatexml', 'extractvalue', 'name_const', 'multipoint', 'database', 'user'),
		'action' => array('@', 'intooutfile', 'intodumpfile', 'unionselect', 'uniondistinct', 'information_schema', 'current_user', 'current_date'),
		'note' => array('/*','*/','#','--'),
	);

	public static function checkquery($sql) {
		$cmd = strtoupper(substr(trim($sql), 0, 6));
		if (in_array($cmd, self::$checkcmd)) {
			$mark = $clean = '';
			$sql = str_replace(array('\\\\', '\\\'', '\\"', '\'\''), '', $sql);
			if (strpos($sql, '/') === false && strpos($sql, '#') === false && strpos($sql, '-- ') === false && strpos($sql, '@') === false && strpos($sql, '`') === false) {
				$cleansql = preg_replace("/'(.+?)'/s", '', $sql);
			} else {
				$cleansql = self::stripSafeChar($sql);
			}
			
			$cleansql = preg_replace("/[^a-z0-9_\-\(\)#\*\/\"]+/is", "", strtolower($cleansql));
			if (is_array(self::$disable['function'])) {
				foreach (self::$disable['function'] as $fun) {
					if (strpos($cleansql, $fun . '(') !== false) {
						return error(1, 'SQL中包含禁用函数 - ' . $fun);
					}
				}
			}
			
			if (is_array(self::$disable['action'])) {
				foreach (self::$disable['action'] as $action) {
					if (strpos($cleansql, $action) !== false) {
						return error(2, 'SQL中包含禁用操作符 - ' . $action);
					}
				}
			}
			
			if (is_array(self::$disable['note'])) {
				foreach (self::$disable['note'] as $note) {
					if (strpos($cleansql, $note) !== false) {
						return error(3, 'SQL中包含注释信息');
					}
				}
			}
		} elseif (substr($cmd, 0, 2) === '/*') {
			return error(3, 'SQL中包含注释信息');
		}
	}
	
	private static function stripSafeChar($sql) {
		$len = strlen($sql);
		$mark = $clean = '';
		for ($i = 0; $i < $len; $i++) {
			$str = $sql[$i];
			switch ($str) {
				case '\'':
					if (!$mark) {
						$mark = '\'';
						$clean .= $str;
					} elseif ($mark == '\'') {
						$mark = '';
					}
					break;
				case '/':
					if (empty($mark) && $sql[$i + 1] == '*') {
						$mark = '/*';
						$clean .= $mark;
						$i++;
					} elseif ($mark == '/*' && $sql[$i - 1] == '*') {
						$mark = '';
						$clean .= '*';
					}
					break;
				case '#':
					if (empty($mark)) {
						$mark = $str;
						$clean .= $str;
					}
					break;
				case "\n":
					if ($mark == '#' || $mark == '--') {
						$mark = '';
					}
					break;
				case '-':
					if (empty($mark) && substr($sql, $i, 3) == '-- ') {
						$mark = '-- ';
						$clean .= $mark;
					}
					break;
				default:
					break;
			}
			$clean .= $mark ? '' : $str;
		}
		return $clean;
	}
}
