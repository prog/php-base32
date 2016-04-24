<?php

namespace com\peterbodnar\base32;



/**
 * Base 32 encoder / decoder
 */
class Base32 {


	const CHARS_RFC4648 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";


	/** @var string */
	protected $alphabet;


	/**
	 * @param string $alphabet ~ Alphabet.
	 */
	public function __construct($alphabet = self::CHARS_RFC4648) {
		$this->alphabet = $alphabet;
	}


	/**
	 * @param string $alphabet ~ Alphabet.
	 */
	public function setAlphabet($alphabet) {
		$this->alphabet = $alphabet;
	}


	/**
	 * Encode data.
	 *
	 * @param string $data ~ Data to encode.
	 * @return string
	 */
	public function encode($data) {
		$hexData = bin2hex($data);
		$hexLen = strlen($hexData);
		$binData = "";
		for ($i=0; $i<$hexLen; $i++) {
			$binData .= str_pad(base_convert($hexData[$i], 16, 2), 4, "0", STR_PAD_LEFT);
		}
		$binLen = strlen($binData);
		$rem = $binLen % 5;
		if ($rem > 0) {
			$pad = 5 - $rem;
			$binData .= str_repeat("0", $pad);
			$binLen += $pad;
		}
		$reslen = $binLen / 5;
		$result = str_repeat("_", $reslen);
		for ($i=0; $i<$reslen; $i += 1) {
			$result[$i] = $this->alphabet[bindec(substr($binData, $i * 5, 5))];
		}
		return $result;
	}


	/**
	 * @param string $data
	 * @return string
	 * @throws Base32Exception
	 */
	public function decode($data) {
		$dataLen = strlen($data);
		$binData = "";
		for ($i=0; $i<$dataLen; $i++) {
			$ord = strpos($this->alphabet, $data[$i]);
			if (FALSE === $ord) {
				$charCode = "0x" . bin2hex($data[$i]);
				throw new Base32Exception("Invalid input char ({$charCode}) at index {$i}");
			}
			$binData .= str_pad(decbin($ord), 5, "0", STR_PAD_LEFT);
		}
		$binLen = strlen($binData);
		$hexLen = floor($binLen / 8) * 2;
		$hexData = str_repeat("_", $hexLen);
		for ($i=0; $i<$hexLen; $i++) {
			$hexData[$i] = base_convert(substr($binData, $i * 4, 4), 2, 16);
		}
		return hex2bin($hexData);
	}

}



class Base32Exception extends \Exception { }
