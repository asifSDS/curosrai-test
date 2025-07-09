<?php
// Allow only valid plugin slugs
$plugins = [
    'car-repair-services-core' => 'https://my.smartdatasoft.com/wp-content/uploads/envato-products/car-repair-services-core.zip',
    'car-repair-demo-installer' => 'https://my.smartdatasoft.com/wp-content/uploads/envato-products/car-repair-demo-installer.zip',
    'estimate-plugin' => 'https://my.smartdatasoft.com/wp-content/uploads/envato-products/estimate-plugin.zip',
];

// Validate request
if (!isset($_GET['plugin']) || !array_key_exists($_GET['plugin'], $plugins)) {
    http_response_code(403);
    exit('Invalid plugin request.');
}

// Plugin download URL
$plugin_slug = $_GET['plugin'];
$plugin_url = $plugins[$plugin_slug];

// Fetch the file from remote and send it
$plugin_data = file_get_contents($plugin_url);
if (!$plugin_data) {
    http_response_code(404);
    exit('Plugin not found.');
}

header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename=\"{$plugin_slug}.zip\"");
echo $plugin_data;
exit;