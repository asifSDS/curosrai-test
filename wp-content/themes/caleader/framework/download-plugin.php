<?php
// Allow only valid plugin slugs
$plugins = [
    'caleader-core' => 'https://my.smartdatasoft.com/wp-content/uploads/envato-products/caleader-core.zip',
    'caleader-demo-installer' => 'https://my.smartdatasoft.com/wp-content/uploads/envato-products/caleader-demo-installer.zip',
    'caleader-compare' => 'https://my.smartdatasoft.com/wp-content/uploads/envato-products/caleader-compare.zip',
    'caleader-listing' => 'https://my.smartdatasoft.com/wp-content/uploads/envato-products/caleader-listing.zip',
    'caleader-review' => 'https://my.smartdatasoft.com/wp-content/uploads/envato-products/caleader-review.zip'
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
$ch = curl_init($plugin_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // in case of redirects
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0'); // mimic browser request
$plugin_data = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($plugin_data === false || $http_code !== 200) {
    http_response_code(404);
    exit('Plugin not found.');
}

header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename=\"{$plugin_slug}.zip\"");
echo $plugin_data;
exit;
