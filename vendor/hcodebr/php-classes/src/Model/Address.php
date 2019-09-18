<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class Address extends Model 
{
	const SESSION = "Address";
	const SESSION_ERROR = "AddressError";
	
	public static function getCEP($nrcep)
	{
		$nrcep = str_replace("-", "", $nrcep);
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/$nrcep/json/");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$data = json_decode(curl_exec($ch), true);
		
		curl_close($ch);
		
		return $data;
	}
	
	public function loadFromCEP($nrcep)
	{
		
		$data = Address::getCEP($nrcep);
		
		if (isset($data['logradouro']) && $data['logradouro']) {
		
			$this->setdesaddress($data['logradouro']);
			$this->setdescomplement($data['complemento']);
			$this->setdesdistrict($data['bairro']);
			$this->setdescity($data['localidade']);
			$this->setdesstate($data['uf']);
			$this->setdescountry('Brasil');
			$this->setdeszipcode($nrcep);
		
		}
		
	}
	
	public function save()
	{
		$sql = new Sql();
		$results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :desnumber, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", [
			':idaddress'=>$this->getidaddress(),
			':idperson'=>$this->getidperson(),
			':desaddress'=>utf8_decode($this->getdesaddress()),
			':desnumber'=>$this->getdesnumber(),
			':descomplement'=>utf8_decode($this->getdescomplement()),
			':descity'=>utf8_decode($this->getdescity()),
			':desstate'=>utf8_decode($this->getdesstate()),
			':descountry'=>utf8_decode($this->getdescountry()),
			':deszipcode'=>$this->getdeszipcode(),
			':desdistrict'=>$this->getdesdistrict()
		]);
		
		
		if (count($results) > 0) {
			$this->setData($results[0]);
		}
	}
	
	public static function setMsgError($msg)
	{
		$_SESSION[Address::SESSION_ERROR] = $msg;
	}
	
	public static function getMsgError()
	{
		$msg = (isset($_SESSION[Address::SESSION_ERROR])) ? $_SESSION[Address::SESSION_ERROR] : "";
		
		Address::clearMsgError();
		
		return $msg;
	}
	
	public static function clearMsgError()
	{
		$_SESSION[Address::SESSION_ERROR] = NULL;
	}
	
	public static function setError($msg)
	{

		$_SESSION[Address::ERROR] = $msg;

	}

	public static function getError()
	{

		$msg = (isset($_SESSION[Address::ERROR]) && $_SESSION[Address::ERROR]) ? $_SESSION[Address::ERROR] : '';

		Address::clearError();

		return $msg;

	}

	public static function clearError()
	{

		$_SESSION[Address::ERROR] = NULL;

	}

	public static function setSuccess($msg)
	{

		$_SESSION[Address::SUCCESS] = $msg;

	}

	public static function getSuccess()
	{

		$msg = (isset($_SESSION[Address::SUCCESS]) && $_SESSION[Address::SUCCESS]) ? $_SESSION[Address::SUCCESS] : '';

		Address::clearSuccess();

		return $msg;

	}

	public static function clearSuccess()
	{

		$_SESSION[Address::SUCCESS] = NULL;

	}

	public static function setErrorRegister($msg)
	{

		$_SESSION[Address::ERROR_REGISTER] = $msg;

	}

	public static function getErrorRegister()
	{

		$msg = (isset($_SESSION[Address::ERROR_REGISTER]) && $_SESSION[Address::ERROR_REGISTER]) ? $_SESSION[Address::ERROR_REGISTER] : '';

		Address::clearErrorRegister();

		return $msg;

	}

	public static function clearErrorRegister()
	{

		$_SESSION[Address::ERROR_REGISTER] = NULL;

	}
	
}