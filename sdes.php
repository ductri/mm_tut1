<?php
	set_time_limit(0);
	ignore_user_abort(1);
	
	define(BLOCK_SIZE, 8);

	/**
	 * Perform permutation.
	 * @param array $array_data Array of 1 or 0, represent data.
	 * @param array $array_permutation Array of 1 or 0, define permutation.
	 * @return A number represents base 2 of result after permutation. Lenght of result
	 * is equal with lenght of @array_data
	 */
	function permute($array_data, $array_permutation) {

		$array_permutation_size = sizeof($array_permutation);
		
		// Product result
		$result = 0;
		
		for ($i = 0; $i < $array_permutation_size; $i++) {
			$result = $result + $array_data[$array_permutation[$i]-1];
			$result = $result << 1;
		}
		
		$result = $result >> 1;

		return $result;
	}
	
	/**
	 * Used to parse a number to array of bits
	 * @param number $number Number to parse 
	 * @param unknown $array_len Lenght of array
	 * @return multitype: array
	 */
	function int2bits($number, $array_len) {
		
		$array_data = array();
		
		//Parse from int number to array of bits
		for ($i = 0; $i < $array_len; $i++) {
			$array_data[$array_len - 1 - $i] = (($number>>1)<<1) ^ $number;
			$number = $number >> 1;
		}
		return $array_data;
	}
	
	/**
	 * Initial permutation
	 * @param string $string A string represent array of 8 bits
	 * @return A number
	 */
	function ip( $string) {
		
		$number = ord($string);
		
		// Array constant
		$p_table = array(2, 6, 3, 1, 4, 8, 5, 7);	
		$array_data = array();
		
		//Parse from int number to array of bits
		for ($i = 0; $i < sizeof($p_table); $i++) {
			$array_data[sizeof($p_table) - 1 - $i] = (($number>>1)<<1) ^ $number;
			$number = $number >> 1;
		}
		
		return permute($array_data, $p_table);
	}

	/**
	 * Perform permutation on $key with lengh 10
	 * @param int $key A int number. Its 10 most right bit will be used. 
	 */
	function p10($key) {
		
		// Constanst array of p10
		$p_table = array(3, 5, 2, 7, 4, 10, 1, 9, 8, 6);
		
		// Array will contain data
		$array_data = array();
		
		// Parse from int number to array to bit with lengh 10
		for ($i = 0; $i < sizeof($p_table); $i++) {
			$array_data[sizeof($p_table) - 1 - $i] = (($key>>1)<<1) ^ $key;
			$key = $key >> 1;
		}
		
		return permute($array_data, $p_table);
	}
	
	/**
	 * Perform permutation on $key with lengh 10
	 * @param int $key A int number. Its 10 most right bit will be used.
	 */
	function p8($key) {
		
		// Constanst array of p10
		$p_table = array(6, 3, 7, 4, 8, 5, 10, 9);
		
		// Array will contain data
		$array_data = array();
		
		// Parse from int number to array to bit with lengh 10
		for ($i = 0; $i < 10; $i++) {
			$array_data[9 - $i] = (($key>>1)<<1) ^ $key;
			$key = $key >> 1;
		}
		return permute($array_data, $p_table);
	}
	/**
	 * Left shift circle each half of $data. This function assume $data has lenght 10
	 * @param int $data A int number. Only 10 right most bits are used. 
	 * @param int $offset Offset to shift
	 */
	function lshift($data, $offset) {
		$l = array();
		$r = array();
		
		// Get 5 most right bit
		for ($i = 0; $i < 5; $i++) {
			$r[4 - $i] = (($data>>1)<<1) ^ $data;
			$data = $data >> 1;
		}
		
		// Shift offset bit
		for ($i = 0; $i < $offset; $i++) {
			$first_bit = $r[0];
			for ($j = 0; $j < 4; $j ++)
				$r[$j] = $r[$j+1];
			$r[4] = $first_bit;
		}
		
		// Get 5 most left bit
		for ($i = 0; $i < 5; $i++) {
			$l[4 - $i] = (($data>>1)<<1) ^ $data;
			$data = $data >> 1;
		}
		
		// Shift offset bit
		for ($i = 0; $i < $offset; $i++) {
			$first_bit = $l[0];
			for ($j = 0; $j < 4; $j ++)
				$l[$j] = $l[$j+1];
			$l[4] = $first_bit;
		}
		
		// Pair left and right
		$result = 0;
		for ($i = 0; $i < 5; $i++) {
			$result = $result + $l[$i];
			$result = $result << 1;
		}
		for ($i = 0; $i < 5; $i++) {
			$result = $result + $r[$i];
			$result = $result << 1;
		}
		$result = $result >> 1;
	
		return $result;
	}
	
	/**
	 * Generate 2 keys into array.
	 * @param int $key A int number. 10 right most bit will be used. 
	 */
	function gen_keys($key) {
		
		$keys = array();
		$p10 = p10($key);
		$ls1 = lshift($p10, 1);
		
		$keys[0] = p8($ls1);
		
		$ls2 = lshift($ls1, 2);
		$keys[1] = p8($ls2);
	
		return $keys;
	}

	/**
	 * Expand and permute
	 * @param int $data An int number. Only 4 right most bit will be used 
	 */
	function expand_permutation($data) {
		// Constanst array of p10
		$p_table = array(4, 1, 2, 3, 2, 3, 4, 1);
		
		// Array will contain data
		$array_data = array();
		
		// Parse from int number to array to bit with lengh 10
		for ($i = 0; $i < 4; $i++) {
			$array_data[3 - $i] = (($data>>1)<<1) ^ $data;
			$data = $data >> 1;
		}
		
		return permute($array_data, $p_table);
	}
	
	/**
	 * 
	 * @param int $data An int number. Only 8 right most bit will be used
	 */
	function s_box($data) {
		$array_bits = int2bits($data, 8);
		
		$l = array();
		for ($i = 0; $i < 4; $i ++) {
			$l[$i] = $array_bits[$i];
		}
		
		$r = array();
		for ($i = 0; $i < 4; $i ++) {
			$r[$i] = $array_bits[$i + 4];
		}
		
		$s0 = array(
				array(1, 0, 3, 2),
				array(3, 2, 1, 0),
				array(0, 2, 1, 3),
				array(3, 1, 3, 2));
		$s1 = array(
				array(0, 1, 2, 3),
				array(2, 0, 1, 3),
				array(3, 0, 1, 0),
				array(2, 1, 0, 3));
		
		
		$row1 = ($l[0]<<1) + $l[3];
		$col1 = ($l[1]<<1) + $l[2];
		$row2 = ($r[0]<<1) + $r[3];
		$col2 = ($r[1]<<1) + $r[2];
		
		$result = ($s0[$row1][$col1] << 2) + $s1[$row2][$col2];
		
		return $result;
	}
	
	/**
	 * 
	 * @param int $data An int number. Only 4 bit right most will be used.
	 */
	function p4($data) {
		$p_table = array(2, 4, 3, 1);
		$array_data = int2bits($data, 4);
		return permute($array_data, $p_table);
	}
	
	/**
	 * 
	 * @param int $right An int number. Only 4 bit right most will be used.
	 * @param int $subkey An int number. Only 8 bit right most will be used.
	 * @return A int number with 4 bit right most is significance.
	 */
	function F_($right, $subkey) {
		$e_p = expand_permutation($right);
		$xor = $e_p ^ $subkey;	
		$s_box = s_box($xor);
		$p4 = p4($s_box);
		return $p4;
	}
	
	/**
	 * 
	 * @param int $key An int number. Only 10 right most bit will be used.
	 * @param int $data An int numner. Only 8 most right bit will be used.
	 * @param int $r Only 4 right most bit will be used.
	 */
	function f($subkey, $data) {
		$r = (($data>>4)<<4) ^ $data;
		$l = $data>>4;
		
		$L = $l ^ F_($r, $subkey);
		$R = $r;
		
		return ($L<<4) + $R; 
	}
	
	/**
	 * Interchange 4 bit left and right of data
	 * @param int $data An int number. Only 8 right most bit will be used.
	 */
	function f_switch($data) {
		$r = (($data>>4)<<4) ^ $data;
		$l = $data>>4;
		return ($r<<4) + $l;
	}
	
	/**
	 * Invert ip
	 * @param int $data An int number. Only 8 right most bit will be used.
	 */
	function ip_1($data) {
		// Array constant
		$p_table = array(4, 1, 3, 5, 7, 2, 8, 6);
		$array_data = int2bits($data, 8);
		
		return permute($array_data, $p_table);
	}
	
	function encrypt_block($block, $key) {
		$ip = ip($block);
		$l = $ip >> 4;
		$r = (($ip >> 4)<<4) ^ $ip;
		
		$sk = gen_keys($key);
		
		$f1 = f($sk[0], $ip);
		$f1 = f_switch($f1);
		$f2 = f($sk[1], $f1);
		$result = ip_1($f2);
		
		return $result;
	}
	
	function decrypt_block($block, $key) {
		
		$l = $ip >> 4;
		$r = (($ip >> 4)<<4) ^ $ip;
		
		$ip = ip($block);
		
		$sk = gen_keys($key);
		
		$f1 = f($sk[1], $ip);
		$f1 = f_switch($f1);
		
		$f2 = f($sk[0], $f1);
		
		$result = ip_1($f2);
		
		return $result;
	}
	
	function encrypt_file($filename, $key) {
		
		$file = fopen($filename, "r") or die("Can't open file");
		$file_size = filesize($filename);
		
		$content = fread($file, $file_size);
		
		$encrypt_file = fopen("result_ciphertext.txt", "w") or die("Can't open file");
		
		for ($i = 0; $i < $file_size; $i ++) {
			$encrypt_block = encrypt_block($content[$i], $key);
			fwrite($encrypt_file, chr($encrypt_block));
		}
		
		fclose($encrypt_file);
		fclose($file);
		print "Encryted data into file: result_ciphertext.txt";
	}
	
	function decrypt_file($filename, $key) {
	
		$file = fopen($filename, "r") or die("Can't open file");
		$file_size = filesize($filename);
	
		$content = fread($file, $file_size);
	
		$encrypt_file = fopen("plaintext.txt", "w") or die("Can't open file");
	
		for ($i = 0; $i < $file_size; $i ++) {
			$encrypt_block = decrypt_block($content[$i], $key);
			fwrite($encrypt_file, chr($encrypt_block));
		}
	
		fclose($encrypt_file);
		fclose($file);
		print "Decryted data into file: plaintext.txt";
	}
	
	?>