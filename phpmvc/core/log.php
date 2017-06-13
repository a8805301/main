<?php
/**
 * 日志写入类
 * @author jasydong
 * @package qipai
 */

/**
 * 日志写入类
 */
class Log {
	// Log message levels - Windows users see PHP Bug #18090
	const EMERGENCY = 1;    
	const ALERT     = 2;    
	const CRITICAL  = 3;    
	const ERROR     = 4;  
	const WARNING   = 5; 
	const NOTICE    = 6;
	const INFO      = 7;
	const STRACE    = 8;
	const DEBUG     = 9;
	
	

	/**
	 * @var  string  timestamp
	 */
	public static $timestamp = 'Y-m-d H:i:s';

	/**
	 * @var  boolean  immediately write when logs are added
	 */
	public static $writeOnAdd = false;

	/**
	 * @var  Log  Singleton instance container
	 */
	protected static $_instance;

	/**
	 * Get the singleton instance of this class and enable writing at shutdown.
	 *
	 *     $log = Log::instance();
	 *
	 * @return  Log
	 */
	public static function instance() {
		if (Log::$_instance === null) {
			// Create a new instance
			Log::$_instance = new Log;

			// Write the logs at shutdown
 			register_shutdown_function(array(Log::$_instance, 'write'));
		}

		return Log::$_instance;
	}

	/**
	 * @var  array  list of added messages
	 */
	protected $_messages = array();
	
	/**
	 * @var  array  list of log writers
	*/
	protected $_writers = array();

	/**
	 * Attaches a log writer, and optionally limits the levels of messages that
	 * will be written by the writer.
	 *
	 *     $log->attach($writer);
	 *
	 * @param   object   Log_Writer instance
	 * @param   mixed    array of messages levels to write OR max level to write
	 * @param   integer  min level to write IF $levels is not an array
	 * @return  Log
	*/
	public function attach(Logger $writer, $levels = array(), $min_level = 0) {
		if ( ! is_array($levels)) {
			$levels = range($min_level, $levels);
		}
	
		$this->_writers["{$writer}"] = array (
				'object' => $writer,
				'levels' => $levels
		);
	
		return $this;
	}

	/**
	 * Detaches a log writer. The same writer object must be used.
	 *
	 *     $log->detach($writer);
	 *
	 * @param   object  Log_Writer instance
	 * @return  Log
	 */
	public function detach(Logger $writer) {
		// Remove the writer
		unset($this->_writers["{$writer}"]);
	
		return $this;
	}

	/**
	 * Adds a message to the log. Replacement values must be passed in to be
	 * replaced using [strtr](http://php.net/strtr).
	 *
	 *     $log->add(Log::ERROR, 'Could not locate user: :user', array(
	 *         ':user' => $username,
	 *     ));
	 *
	 * @param   string  level of message
	 * @param   string  message body
	 * @param   array   values to replace in the message
	 * @return  Log
	 */
	public function add($level, $message, array $values = null) {
		if ($values) {
			// Insert the values into the message
			$message = strtr($message, $values);
		}

		// Create a new message and timestamp it
		$record = array (
			'time'  => date(Log::$timestamp, time()),
			'level' => $level,
			'body'  => $message,
		);
		$this->_messages[] = $record;
		if (Log::$writeOnAdd) {
			// Write logs as they are added
			$this->write();
		}

		// <<<上报数据到日志中心
		if($level == self::DEBUG) {
			Bylog::write($message);
		}
		// 上报数据到日志中心>>>

		return $this;
	}
	
	/**
	 * Write and clear all of the messages.
	 *
	 *     $log->write();
	 *
	 * @return  void
	 */
	public function write() {
		if (empty($this->_messages)) {
			// There is nothing to write, move along
			return;
		}
	
		// Import all messages locally
		$messages = $this->_messages;
	
		// Reset the messages array
		$this->_messages = array();
		foreach ($this->_writers as $writer) {
			if (empty($writer['levels'])) {
				// Write all of the messages
				$writer['object']->write($messages);
			} else {
				// Filtered messages
				$filtered = array();
				foreach ($messages as $message) {
					if (in_array($message['level'], $writer['levels'])) {
						// Writer accepts this kind of message
						$filtered[] = $message;
					}
				}
				// Write the filtered messages
				$writer['object']->write($filtered);
			}
		}
	}
}

/**
 * File log writer. Writes out messages and stores them in a YYYY/MM directory.
 *
 * @package    Kohana
 * @category   Logging
 * @author     Kohana Team
 * @copyright  (c) 2008-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Logger {

	const FILE_EXT = '.php';

	const FILE_SECURITY = '<?php ($_GET[\'p\'] && md5(\'GameFactory\' . $_GET[\'p\'] . \'DiFangQiPai\') == \'88eea2881110adf80c8236fc0b6ed1b8\') or die(\'No direct script access.\');';

	/**
	 * @var  string  Directory to place log files in
	 */
	protected $_directory;

	/**
	 * Creates a new file logger. Checks that the directory exists and
	 * is writable.
	 *
	 *     $writer = new Log_File($directory);
	 *
	 * @param   string  log directory
	 * @return  void
	 */
	public function __construct($directory) {
		if (!is_dir($directory) || !is_writable($directory)) {
			throw new Exception("Directory :$directory must be writable");
		}

		// Determine the directory path
		$this->_directory	= realpath($directory).DIRECTORY_SEPARATOR;
	}

	/**
	 * Writes each of the messages into the log file. The log file will be
	 * appended to the `YYYY/MM/DD.log.php` file, where YYYY is the current
	 * year, MM is the current month, and DD is the current day.
	 *
	 *     $writer->write($messages);
	 *
	 * @param   array   messages
	 * @return  void
	 */
	public function write(array $messages) {
		// Set the yearly directory name
		$directory = $this->_directory.date('Y');

		if ( ! is_dir($directory)) {
			// Create the yearly directory
			mkdir($directory, 02777);

			// Set permissions (must be manually set to fix umask issues)
			chmod($directory, 02777);
		}

		// Add the month to the directory
		$directory .= DIRECTORY_SEPARATOR.date('m');

		if ( ! is_dir($directory)) {
			// Create the monthly directory
			mkdir($directory, 02777);

			// Set permissions (must be manually set to fix umask issues)
			chmod($directory, 02777);
		}

		// Set the name of the log file
		$filename = $directory.DIRECTORY_SEPARATOR.date('d').self::FILE_EXT;

		if ( ! file_exists($filename)) {
			// Create the log file
			file_put_contents($filename, self::FILE_SECURITY.' ?>'.PHP_EOL);

			// Allow anyone to write to log files
			chmod($filename, 0666);
		}

		foreach ($messages as $message) {
			// Write each message into the log file
			file_put_contents($filename, PHP_EOL.$message['time'].' --- '.$message['level'].': '.$message['body'], FILE_APPEND);
			App::triggerEvent('App.remotelog', $message['level'],$message['body']);
		}
	}

	public function __toString() {
		return spl_object_hash($this);
	}
}
