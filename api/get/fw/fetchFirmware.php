<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) 
{
  http_response_code(403);
  exit();
}

function fetchFirmware()
{
  $repo_owner = 'QVEX-Tech';
  $repo_name = 'Lynepad-FW';
  $asset_filter = '.hex';
  $destination_folder = 'firmware-files';

  // get latest release from Github API
  $api_url = "https://api.github.com/repos/$repo_owner/$repo_name/releases/latest";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $api_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
  $result = curl_exec($ch);
  curl_close($ch);

  $release = json_decode($result, true);
  $assets = $release['assets'];

  // loop through assets and download files with .hex extension
  foreach ($assets as $asset) 
  {
    if (strpos($asset['name'], $asset_filter) !== false) 
    {
      $filename = $destination_folder . '/' . $asset['name'];
      if (!file_exists($filename)) 
      {
        $file = file_get_contents($asset['browser_download_url']);
        file_put_contents($filename, $file);
      }
    }
  }
}



?>