<?php  

if(!class_exists('openSRS_crypt'))
{
    class openSRS_crypt {
            var $known_ciphers = array (
                    'DES'               => MCRYPT_DES,
                    'BLOWFISH'          => MCRYPT_BLOWFISH,
                    'BLOWFISH-COMPAT'   => MCRYPT_BLOWFISH_COMPAT,
            );


            var $cipher;					// used cipher - @var string
            var $TD;						// crypt resource, for 2.4.x @var string
            var $deinit_function;			// crypt deinit function, for backwards compatability - @var string
            var $blocksize;					// blocksize of cipher - @var string
            var $keysize;					// keysize of cipher - @var int
            var $keyhash;					// mangled key - @var string
            var $rand_source = MCRYPT_RAND;	// - source type of the initialization vector for creation   - possible types are MCRYPT_RAND or MCRYPT_DEV_URANDOM or MCRYPT_DEV_RANDOM     - @var int
            var $header_spec = 'RandomIV';	// - header - @var string
            var $_last_clear;				// - debugging - @var string
            var $_last_crypt;				// - debugging - @var string


            function openSRS_crypt ($key, $cipher='DES') {
                    if (!extension_loaded('mcrypt')) throw new Exception ("oSRS Error - mcrypt module is not compiled into PHP");
                    if (!function_exists('mcrypt_module_open')) throw new Exception ("oSRS Error - libmcrypt version insufficient");

                    if (function_exists('mcrypt_generic_deinit')) {
                            $this->deinit_function = 'mcrypt_generic_deinit';
                    } else if (function_exists('mcrypt_generic_end')) {
                            $this->deinit_function = 'mcrypt_generic_end';
                    } else {
                            throw new Exception ("oSRS Error - PHP version insufficient");
                    }

                    srand ((double)microtime()*1000000);
                    $this->header_spec = 'RandomIV';
                    if (!$key) throw new Exception ("oSRS Error - no key specified");

                    $cipher = strtoupper($cipher);					// check for cipher
                    if (!isset($this->known_ciphers[$cipher])) throw new Exception ("oSRS Error - unknown cipher - ". $cipher);
                    $this->cipher = $this->known_ciphers[$cipher];

                    // initialize cipher
                    $this->TD = mcrypt_module_open ($this->cipher, '', 'ecb', '');
                    $this->blocksize = mcrypt_enc_get_block_size($this->TD);
                    $this->keysize = mcrypt_enc_get_key_size($this->TD);

                    // mangle key with MD5
                    $this->keyhash = $this->_md5perl($key);
                    while( strlen($this->keyhash) < $this->keysize ) {
                            $this->keyhash .= $this->_md5perl($this->keyhash);
                    }
                    $this->key = substr($this->keyhash, 0, $this->keysize);
                    return true;
            }


            // - Destructor
            function _openSRS_crypt () {
                    @mcrypt_module_close($this->TD);
            }


            function encrypt($clear) {
                    $this->last_clear = $clear;
                    $iv = mcrypt_create_iv($this->blocksize, $this->rand_source);			/* new IV for each message */
                    $crypt = $this->header_spec . $iv;										/* create the message header */
                    $padsize = $this->blocksize - (strlen($clear) % $this->blocksize);		/* pad the cleartext */
                    $clear .= str_repeat(pack ('C*', $padsize), $padsize);

                    // do the encryption
                    $start = 0;
                    while ( $block = substr($clear, $start, $this->blocksize) ) {
                            $start += $this->blocksize;
                            if (mcrypt_generic_init($this->TD, $this->key, $iv) < 0 ) throw new Exception ("oSRS Error - encrypt - mcrypt_generic_init failed");
                            $cblock = mcrypt_generic($this->TD, $iv^$block );
                            $iv = $cblock;
                            $crypt .= $cblock;
                            call_user_func($this->deinit_function, $this->TD);
                    }

                    $this->last_crypt = $crypt;
                    return $crypt;
            }


            function decrypt($crypt) {
                    $this->last_crypt = $crypt;

                    $iv_offset = strlen($this->header_spec);		/* get the IV from the message header */
                    $header = substr($crypt, 0, $iv_offset);
                    $iv = substr ($crypt, $iv_offset, $this->blocksize);
                    if ( $header != $this->header_spec ) throw new Exception ("oSRS Error - no initialization vector");

                    $crypt = substr($crypt, $iv_offset+$this->blocksize);

                    /* decrypt the message */
                    $start = 0;
                    $clear = '';

                    while ( $cblock = substr($crypt, $start, $this->blocksize) ) {
                            $start += $this->blocksize;
                            if (mcrypt_generic_init($this->TD, $this->key, $iv) < 0 ) throw new Exception ("oSRS Error - decrypt - mcrypt_generic_init failed");
                            $block = $iv ^ mdecrypt_generic($this->TD, $cblock);
                            $iv = $cblock;
                            $clear .= $block;
                            call_user_func($this->deinit_function, $this->TD);
                    }

                    /* remove the padding from the end of the cleartext */
                    $padsize = ord(substr($clear, -1));
                    $clear = substr($clear, 0, -$padsize);

                    $this->last_clear = $clear;
                    return $clear;
            }

            function _md5perl($string) {
                    return pack('H*', md5($string));
            }
    }
}