<?php

namespace Spl\Security;

class HMAC
{
	
	public static function secret(int $length = 32, bool $outputAsBase64UrlEncodedData = true, bool $removePaddingInOutputData = true) 
    {
		
		try {
			
			if(!isset($length) || intval($length) <= 8 ){
			  
			  $length = 32;
			  
			}
			
			if (function_exists('random_bytes')) {
				
				$generatedCryptographicallySecureString = random_bytes($length);
				
			} else if (function_exists('openssl_random_pseudo_bytes')) {
				
				$generatedCryptographicallySecureString = openssl_random_pseudo_bytes($length);
				
			}
			
			if ((isset($generatedCryptographicallySecureString)) && ($generatedCryptographicallySecureString != "")) {
				
				if ($outputAsBase64UrlEncodedData === true) {
					
					$base64UrlEncodedSecret = static::encode($generatedCryptographicallySecureString);
					
					if ($removePaddingInOutputData === true) {
						
						$base64UrlEncodedPaddingRemovedSecret = static::filter($base64UrlEncodedSecret);
						
						return $base64UrlEncodedPaddingRemovedSecret;
						
					} else {
						
						return $base64UrlEncodedSecret;
						
					}
					
				} else {
					
					$binarySecret = openssl_random_pseudo_bytes($length);
					
					return $binarySecret;
					
				}
				
			} else {
				
				throw new \Exception("Error generating Cryptographically Secure String!");
				
			}
			
			
		} catch (\Exception $e) {
			
			throw new \Exception($e->getMessage(), (int)$e->getCode());
			
		}
		
	}
	
	public static function hash(string $message, string $secretKey, string $algorithm = 'sha256', bool $outputAsBase64UrlEncodedData = true, bool $removePaddingInOutputData = true) 
    {
		
		try {
		
			if (in_array($algorithm, hash_hmac_algos(), true)) {
		
				if ($outputAsBase64UrlEncodedData === true) {
					
					//Binary Signature
					$binarySignature = hash_hmac($algorithm, $message, $secretKey, true);
					
					//Base64 URL Encoded Binary Signature
					$base64UrlEncodedBinarySignature = static::encode($binarySignature);
					
					if ($removePaddingInOutputData === true) {
						
						//remove padding (=), from Base64 URL Encoded Binary Signature
						$base64UrlEncodedPaddingRemovedBinarySignature = static::filter($base64UrlEncodedBinarySignature);
						
						return $base64UrlEncodedPaddingRemovedBinarySignature;
						
						
					} else {
						
						return $base64UrlEncodedBinarySignature;
						
					}
					
				} else {
					
					//Binary Signature
					return hash_hmac($algorithm, $message, $secretKey, true);
					
				}
			
			} else {
				
				throw new \Exception("Invalid Algorithm submitted!, please use a HMAC supported Hash Algorithm. \n");
				
			}
			
		} catch (\Exception $e) {
			
			throw new \Exception($e->getMessage(), (int)$e->getCode());
			
		}
		
	}
	
	public static function verify(string $systemGeneratedSignature, string $userSuppliedSignature) {
		
		try {
			
			if ((function_exists('hash_equals')) && (hash_equals($systemGeneratedSignature, $userSuppliedSignature))) {
				
				return true;
				
			} else {
				
				return false;
				
			}
			
		} catch (\Exception $e) {
			
			throw new \Exception($e->getMessage(), (int)$e->getCode());
			
		}
		
	}
	
	public static function encode(string $string) {
		
		return str_replace(array('+', '/'), array('-', '_'), base64_encode($string));
		
	}
	
	public static function decode(string $string) {
		
		return base64_decode(str_replace(array('-', '_'), array('+', '/'), $string));
		
	}
	
	public static function filter(string $string) {
		
		//remove padding (=), from Base64 URL Encoded Binary Signature
		return str_replace("=", "", $string);
		
	}
	
	
}