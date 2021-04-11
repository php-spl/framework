<?php

namespace Web\Security;

class Hash
{
    static $algo = 'sha256';

	public static function crypt($string, $salt = '') 
    {
        return crypt($string . $salt, '$2y$10$' . $salt);
    }

    public static function random($length = 32) 
    {
        return strtr(substr(base64_encode(openssl_random_pseudo_bytes($length)),0,22), '+', '.');
    }

    public static function make($string, $key = false, $random = false) 
    {
        if($key) {
            return hash(self::$algo, $string . $key);
        }

        if($random) {
            return hash(self::$algo, $string . self::random());
        }
        
        return hash(self::$algo, $string);
    }

    public static function unique() 
    {
        return self::make(uniqid());
    }

    public static function equals($hash, $sig)
    {
       return hash_equals(self::make($hash), $sig);
    }

    public static function uuid_v4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff )
        );
    }

    
    /**
	 * RFC 4122 compliant UUID version 5.
     * 
     * The RFC 4122 specification defines a Uniform Resource Name namespace for
     * UUIDs (Universally Unique IDentifier), also known as GUIDs (Globally
     * Unique IDentifier).  A UUID is 128 bits long, and requires no central
     * registration process.
     * 
     * NameSpace_DNS: {6ba7b810-9dad-11d1-80b4-00c04fd430c8}
     * NameSpace_URL: {6ba7b811-9dad-11d1-80b4-00c04fd430c8}
     * NameSpace_OID: {6ba7b812-9dad-11d1-80b4-00c04fd430c8}
     * NameSpace_X500:{6ba7b814-9dad-11d1-80b4-00c04fd430c8}
	 *
	 * @param  string $name    The name to generate the UUID from.
	 * @param  string $ns_uuid Namespace UUID. Default is for the NS when name string is a URL.
	 * @return string The UUID string.
	 */
	public function uuid_v5( $name, $ns_uuid = '6ba7b811-9dad-11d1-80b4-00c04fd430c8' ) {

		// Compute the hash of the name space ID concatenated with the name.
		$hash = sha1( $ns_uuid . $name );

		// Intialize the octets with the 16 first octets of the hash, and adjust specific bits later.
		$octets = str_split( substr( $hash, 0, 16 ), 1 );

		/*
		 * Set version to 0101 (UUID version 5).
		 *
		 * Set the four most significant bits (bits 12 through 15) of the
		 * time_hi_and_version field to the appropriate 4-bit version number
		 * from Section 4.1.3.
		 *
		 * That is 0101 for version 5.
		 * time_hi_and_version is octets 6–7
		 */
		$octets[6] = chr( ord( $octets[6] ) & 0x0f | 0x50 );

		/*
		 * Set the UUID variant to the one defined by RFC 4122, according to RFC 4122 section 4.1.1.
		 *
		 * Set the two most significant bits (bits 6 and 7) of the
		 * clock_seq_hi_and_reserved to zero and one, respectively.
		 *
		 * clock_seq_hi_and_reserved is octet 8
		 */
		$octets[8] = chr( ord( $octets[8] ) & 0x3f | 0x80 );

		// Hex encode the octets for string representation.
		$octets = array_map( 'bin2hex', $octets );

		// Return the octets in the format specified by the ABNF in RFC 4122 section 3.
		return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( implode( '', $octets ), 4 ) );
	}

}