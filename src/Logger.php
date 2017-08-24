<?php

namespace Lncknight;

class Logger {
	
	public $base_dir;
	
	/**
	 * Logger constructor.
	 * @param $base_dir
	 */
	public function __construct($base_dir = null)
	{
		if ($base_dir){
			$this->base_dir = $base_dir;
		}
	}
	
	/**
	 * @param $base_dir
	 * @return $this
	 */
	public function setBaseDir($base_dir)
	{
		$this->base_dir = $base_dir;
		return $this;
	}
	
	/**
	 * @param $message
	 * @param null $namespace
	 * @throws LoggerException
	 */
	public function log($message, $namespace = null){
		
		$msg = $message;
		
		$date = date('Y-m-d H:i:s');
		$prepand_content = "{$date} - ";
		if (is_array($msg) || is_object($msg)) {
			$msg = $prepand_content . "\r\n" . print_r($msg,1);
		}
		else {
			$msg = $prepand_content . ' - ' . $msg;
		}
		$msg .= "\r\n";
		
		$section_name = strlen($namespace) ? $namespace : 'default';
		
		if (!$this->base_dir){
			throw new LoggerException('`log_base` dir not defined');
		}
		
		$log_dir = $this->base_dir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
		$log_file = $log_dir . $section_name . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
		
		self::mkdir(dirname($log_file));
		
		// write file
		if (!file_exists($log_file)) {
			@touch($log_file);
			@chmod($log_file, 0777);
		}
		
		if (is_writable($log_file))
		{
			if (!$handle = fopen($log_file, 'a')) {
				return;
			}
		}
		else
			return;
		
		if (fwrite($handle, $msg) === FALSE) {
			return;
		}
		fclose($handle);
		
		return ;
	}
	
	/**
	 * @param $dir_path
	 * @param int $mode
	 * @return bool
	 */
	private static function mkdir($dir_path, $mode = 0777){
		if (!file_exists($dir_path)){
			$umask = umask(0);
			mkdir($dir_path, $mode, true);
			umask($umask);
		}
		
		return true;
	}
}