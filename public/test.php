<?php
// Simple test file to verify PHP environment and extensions in the browser
echo "<h1>PHP Environment Test</h1>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled') . "<br>";
echo "BCMath: " . (extension_loaded('bcmath') ? 'Enabled' : 'Disabled') . "<br>";
echo "XML: " . (extension_loaded('xml') ? 'Enabled' : 'Disabled') . "<br>";
echo "Ctype: " . (extension_loaded('ctype') ? 'Enabled' : 'Disabled') . "<br>";
echo "OpenSSL: " . (extension_loaded('openssl') ? 'Enabled' : 'Disabled') . "<br>";
echo "JSON: " . (extension_loaded('json') ? 'Enabled' : 'Disabled') . "<br>";
