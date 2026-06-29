<?php
echo "CRYPT_SHA512: " . (defined('CRYPT_SHA512') ? (CRYPT_SHA512 ? 'yes' : 'no') : 'undef') . "\n";
echo "Test: " . crypt('admin', '$6$rounds=5000$salt_salt_salt_$zzBipONnoRx4eUBxJIiU./KadaCtmBxFd/zwvQYorFgIvR2sJzAx/ZoOhlpTJsJ94wHgl/EohS5BLrqUYDZSm0') . "\n";
echo "PHP: " . phpversion() . "\n";
