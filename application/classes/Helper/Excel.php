<?php defined('SYSPATH') or die('No direct access allowed.');
/***********************************************************
* Excel.php - Helper
* Used to open Excel files for speady reading
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/



class Helper_Excel
{

	/**
	 * Used to open files for reading data only. This reduces memory usage,
	 * but means we can't write or read formatting
	 * @param string $file_name fully qualified path to the Excel file to be read
	 * @return PHPExcel object for the Excel file
	 */
	public static function open_for_reading_data($file_name)
	{
		//get the PHPExcel classes on stand by:
		require_once Kohana::find_file('PHPExcel', 'Classes/PHPExcel');
		PHPExcel_CachedObjectStorageFactory::initialize(PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp); //reduce memory usage
		$reader = PHPExcel_IOFactory::createReaderForFile($file_name);
		$reader->setReadDataOnly(true); //supposed to reduce memory usage
		$excel = $reader->load($file_name);
		return $excel;
	}
}//end class
