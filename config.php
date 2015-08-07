<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_lifetime', 0); 
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.entropy_length',  512);
ini_set('session.hash_function', 'sha512');
ini_set('session.use_only_cookies', 1);
ini_set('session.hash_bits_per_character', 6);
/*session_name('sid');*/
?>