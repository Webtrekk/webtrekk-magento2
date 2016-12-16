<?php
/**
 * @author Webtrekk Team
 * @copyright Copyright (c) 2016 Webtrekk GmbH (https://www.webtrekk.com)
 * @package Webtrekk_TagIntegration
 */
namespace Webtrekk\TagIntegration\Helper;


class DataLayer
{
	/**
     * @var string
     */
	const DATA_DELIMITER = ';';
	
	
	/**
     * @param array $data
	 *
	 * @return integer
     */
	private static function getItemCount(array $data = [])
	{
		if(isset($data['id'])) {
			return count(explode(self::DATA_DELIMITER, $data['id']));
		}
		
		return 0;
	}
	
	
	/**
     * @param string $value1
	 * @param string $value2
	 * @param integer $itemCount
	 *
	 * @return string
     */
	private static function getFormatedString($value1, $value2, $itemCount)
	{
		$data = [];
		if(!empty($value1)) {
			$data = explode(self::DATA_DELIMITER, $value1);
		}
		
		for($i = count($data); $i < $itemCount; $i++) {
			$data[] = '';
		}
		$data[] = $value2;
		
		return implode(self::DATA_DELIMITER, $data);
	}
	
	
	/**
     * @param mixed $data
	 *
	 * @return array
     */
	private static function convertToArray($data)
	{
		if(is_array($data)) {
			return $data;
		}
		else if($data) {
			return [$data];
		}
		
		return [];
	}
	
	
	/**
     * @param mixed $data
	 *
	 * @return object
     */
	private static function convertToObject($data)
	{
		if(is_object($data)) {
			return $data;
		}
		else if(is_array($data)) {
			return (object)$data;
		}
		
		return (object)[];
	}
	
	
	/**
     * @param mixed $data1
	 * @param mixed $data2
	 * @param integer|null $itemCount
	 *
	 * @return array|object
     */
	public static function merge($data1, $data2, $itemCount = null)
	{
		if($itemCount === null) {
			$itemCount = self::getItemCount($data1);
		}
		
		if(is_object($data1) || is_object($data2)) {
			$data1 = self::convertToObject($data1);
			$data2 = self::convertToObject($data2);
		}
		else if(is_array($data1) || is_array($data2)) {
			$data1 = self::convertToArray($data1);
			$data2 = self::convertToArray($data2);
		}
		
		foreach($data2 as $key => $value) {
			if(is_array($value) || is_object($value)) {
				$data = ((isset($data1[$key])) ? $data1[$key] : []);
				$data1[$key] = self::merge($data, $value, $itemCount);
			}
			else {
				if(isset($data1[$key])) {
					if(is_array($data1[$key]) || is_object($data1[$key])) {
						$data1[$key] = self::merge($data1[$key], $value, $itemCount);
					}
					else {
						$data1[$key] = self::getFormatedString($data1[$key], $value, $itemCount);
					}
				}
				else {
					$data1[$key] = self::getFormatedString("", $value, $itemCount);
				}
			}
		}
		
		return $data1;
	}
	
}