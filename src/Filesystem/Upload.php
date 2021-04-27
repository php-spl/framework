<?php

namespace Spl\Filesystem;

use Exception;
use finfo;

class Upload 
{


	/**
	 * Default directory persmissions (destination dir)
	 */
	protected $defaultPermissions = 0750;


	/**
	 * File post array
	 *
	 * @var array
	 */
	protected $filePost = array();


	/**
	 * Destination directory
	 *
	 * @var string
	 */
	protected $destination;


	/**
	 * Fileinfo
	 *
	 * @var object
	 */
	protected $finfo;


	/**
	 * Data about file
	 *
	 * @var array
	 */
	public $file = array();


	/**
	 * Max. file size
	 *
	 * @var int
	 */
	protected $maxFileSize;


	/**
	 * Allowed mime types
	 *
	 * @var array
	 */
	protected $mimes = array();


	/**
	 * External callback object
	 *
	 * @var object
	 */
	protected $externalCallbackObject;


	/**
	 * External callback methods
	 *
	 * @var array
	 */
	protected $externalCallbackMethods = array();


	/**
	 * Temp path
	 *
	 * @var string
	 */
	protected $tmpName;


	/**
	 * Validation errors
	 *
	 * @var array
	 */
	protected $validationErrors = array();


	/**
	 * Filename (new)
	 *
	 * @var string
	 */
	protected $filename;


	/**
	 * Internal callbacks (filesize check, mime, etc)
	 *
	 * @var array
	 */
	private $callbacks = array();

	/**
	 * Root dir
	 *
	 * @var string
	 */
	protected $root;

	/**
	 * Return upload object
	 *
	 * $destination		= 'path/to/your/file/destination/folder';
	 *
	 * @param string $destination
	 * @param string $root
	 * @return Upload
	 */
	public static function factory($destination, $root = false) {

		return new Upload($destination, $root);

	}


	/**
	 *  Define ROOT constant and set & create destination path
	 *
	 * @param string $destination
	 * @param string $root
	 */
	public function __construct($destination, $root = false) {

		if ($root) {

			$this->root = $root;

		} else {

			$this->root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
		}

		// set & create destination path
		if (!$this->setDestination($destination)) {

			throw new Exception('Upload: Can\'t create destination. '.$this->root . $this->destination);

		}

		//create finfo object
		$this->finfo = new finfo();

	}

	/**
	 * Set target filename
	 *
	 * @param string $filename
	 */
	public function setFilename($filename) {

		$this->filename = $filename;

	}

	/**
	 * Check & Save file
	 *
	 * Return data about current upload
	 *
	 * @return array
	 */
	public function upload($filename = false) {

		if( $filename ) {

			$this->setFilename($filename);

		}

		$this->setFilename($filename);
		
		if ($this->check()) {

			$this->save();

		}

		// return state data
		return $this->getState();

	}


	/**
	 * Save file on server
	 *
	 * Return state data
	 *
	 * @return array
	 */
	public function save() {

		$this->saveFile();

		return $this->getState();

	}


	/**
	 * Validate file (execute callbacks)
	 *
	 * Returns TRUE if validation successful
	 *
	 * @return bool
	 */
	public function check() {

		//execute callbacks (check filesize, mime, also external callbacks
		$this->validate();

		//add error messages
		$this->file['errors'] = $this->getErrors();

		//change file validation status
		$this->file['status'] = empty($this->validationErrors);

		return $this->file['status'];

	}


	/**
	 * Get current state data
	 *
	 * @return array
	 */
	public function getState() {

		return $this->file;

	}


	/**
	 * Save file on server
	 */
	protected function saveFile() {

		//create & set new filename
		if(empty($this->filename)){
			$this->createNewFilename();
		}

		//set filename
		$this->file['filename']	= $this->filename;

		//set full path
		$this->file['full_path'] = $this->root . $this->destination . $this->filename;
        	$this->file['path'] = $this->destination . $this->filename;

		$status = move_uploaded_file($this->tmpName, $this->file['full_path']);

		//checks whether upload successful
		if (!$status) {
			throw new Exception('Upload: Can\'t upload file.');
		}

		//done
		$this->file['status']	= true;

	}


	/**
	 * Set data about file
	 */
	protected function setFileData() {

		$file_size = $this->getFileSize();

		$this->file = array(
			'status'				=> false,
			'destination'			=> $this->destination,
			'size_in_bytes'			=> $file_size,
			'size_in_mb'			=> $this->bytesToMb($file_size),
			'mime'					=> $this->getFileMime(),
			'original_filename'		=> $this->filePost['name'],
			'tmp_name'				=> $this->filePost['tmp_name'],
			'post_data'				=> $this->filePost,
		);

	}

	/**
	 * Set validation error
	 *
	 * @param string $message
	 */
	public function setError($message) {

		$this->validationErrors[] = $message;

	}


	/**
	 * Return validation errors
	 *
	 * @return array
	 */
	public function getErrors() {

		return $this->validationErrors;

	}


	/**
	 * Set external callback methods
	 *
	 * @param object $instance_of_callback_object
	 * @param array $callback_methods
	 */
	public function callbacks($instance_of_callback_object, $callback_methods) {

		if (empty($instance_of_callback_object)) {

			throw new Exception('Upload: $instance_of_callback_object can\'t be empty.');

		}

		if (!is_array($callback_methods)) {

			throw new Exception('Upload: $callback_methods data type need to be array.');

		}

		$this->externalCallbackObject	 = $instance_of_callback_object;
		$this->externalCallbackMethods = $callback_methods;

	}


	/**
	 * Execute callbacks
	 */
	protected function validate() {

		//get curent errors
		$errors = $this->getErrors();

		if (empty($errors)) {

			//set data about current file
			$this->setFileData();

			//execute internal callbacks
			$this->executeCallbacks($this->callbacks, $this);

			//execute external callbacks
			$this->executeCallbacks($this->externalCallbackMethods, $this->externalCallbackObject);

		}

	}


	/**
	 * Execute callbacks
	 */
	protected function executeCallbacks($callbacks, $object) {

		foreach($callbacks as $method) {

			$object->$method($this);

		}

	}


	/**
	 * File mime type validation callback
	 *
	 * @param object $object
	 */
	protected function checkMimeType($object) {

		if (!empty($object->mimes)) {

			if (!in_array($object->file['mime'], $object->mimes)) {

				$object->setError('Mime type not allowed.');

			}

		}

	}


	/**
	 * Set allowed mime types
	 *
	 * @param array $mimes
	 */
	public function setAllowedMimeTypes($mimes) {

		$this->mimes		= $mimes;

		//if mime types is set -> set callback
		$this->callbacks[]	= 'check_mime_type';

	}


	/**
	 * File size validation callback
	 *
	 * @param object $object
	 */
	protected function checkFileSize($object) {

		if (!empty($object->maxFileSize)) {

			$file_size_in_mb = $this->bytesToMb($object->file['size_in_bytes']);

			if ($object->maxFileSize <= $file_size_in_mb) {

				$object->setError('File is too big.');

			}

		}

	}


	/**
	 * Set max. file size
	 *
	 * @param int $size
	 */
	public function setMaxFileSize($size) {

		$this->maxFileSize	= $size;

		//if max file size is set -> set callback
		$this->callbacks[]	= 'check_file_size';

	}


	/**
	 * Set File array to object
	 *
	 * @param array $file
	 */
	public function file($file) {

		$this->setFileArray($file);

	}


	/**
	 * Set file array
	 *
	 * @param array $file
	 */
	protected function setFileArray($file) {

		//checks whether file array is valid
		if (!$this->checkFileArray($file)) {

			//file not selected or some bigger problems (broken files array)
			$this->setError('Please select file.');

		}

		//set file data
		$this->filePost = $file;

		//set tmp path
		$this->tmpName  = $file['tmp_name'];

	}


	/**
	 * Checks whether Files post array is valid
	 *
	 * @return bool
	 */
	protected function checkFileArray($file) {

		return isset($file['error'])
			&& !empty($file['name'])
			&& !empty($file['type'])
			&& !empty($file['tmp_name'])
			&& !empty($file['size']);

	}


	/**
	 * Get file mime type
	 *
	 * @return string
	 */
	protected function getFileMime() {

		return $this->finfo->file($this->tmpName, FILEINFO_MIME_TYPE);

	}


	/**
	 * Get file size
	 *
	 * @return int
	 */
	protected function getFileSize() {

		return filesize($this->tmpName);

	}


	/**
	 * Set destination path (return TRUE on success)
	 *
	 * @param string $destination
	 * @return bool
	 */
	protected function setDestination($destination) {

		$this->destination = $destination . DIRECTORY_SEPARATOR;

		return $this->destinationExist() ? TRUE : $this->createDestination();

	}


	/**
	 * Checks whether destination folder exists
	 *
	 * @return bool
	 */
	protected function destinationExist() {

		return is_writable($this->root . $this->destination);

	}


	/**
	 * Create path to destination
	 *
	 * @param string $dir
	 * @return bool
	 */
	protected function createDestination() {

		return mkdir($this->root . $this->destination, $this->defaultPermissions, true);

	}


	/**
	 * Set unique filename
	 *
	 * @return string
	 */
	protected function createNewFilename() 
	{
		$filename = sha1(mt_rand(1, 9999) . $this->destination . uniqid()) . time();
		$this->setFilename($filename);
	}


	/**
	 * Convert bytes to mb.
	 *
	 * @param int $bytes
	 * @return int
	 */
	protected function bytesToMb($bytes) 
	{
		return round(($bytes / 1048576), 2);
	}


} // end of Upload
