<?php
// Proxy público para ../src/get_similar_properties.php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
	require_once __DIR__ . '/../vendor/autoload.php';
} else {
	if (file_exists(__DIR__ . '/../src/config/settings.php')) {
		require_once __DIR__ . '/../src/config/settings.php';
	}
	if (file_exists(__DIR__ . '/../src/config/database.php')) {
		require_once __DIR__ . '/../config/database.php';
	}
}

require_once __DIR__ . '/../src/get_similar_properties.php';
