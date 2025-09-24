<?php

// Read current package.json
$packageJson = json_decode(file_get_contents('package.json'), true);
$currentVersion = $packageJson['version'];

// Increment patch version
$versionParts = explode('.', $currentVersion);
$versionParts[2] = (int)$versionParts[2] + 1;
$newVersion = implode('.', $versionParts);

// Update package.json
$packageJson['version'] = $newVersion;
$jsonOutput = json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// Convert 4-space indentation to 2-space
$jsonOutput = preg_replace_callback('/^( +)/m', function ($matches) {
    $spaces = strlen($matches[1]);
    $newSpaces = str_repeat(' ', $spaces / 2);

    return $newSpaces;
}, $jsonOutput);
file_put_contents('package.json', $jsonOutput . "\n");

// Update plugin header
$pluginFile = file_get_contents('scheduled-modal.php');
$pluginFile = preg_replace('/^(\s*\*\s*Version:\s*)[0-9.]+/m', '${1}' . $newVersion, $pluginFile);
file_put_contents('scheduled-modal.php', $pluginFile);

// Update readme.txt stable tag
$readmeFile = file_get_contents('readme.txt');
$readmeFile = preg_replace('/Stable tag:\s*[0-9.]+/', 'Stable tag: ' . $newVersion, $readmeFile);
file_put_contents('readme.txt', $readmeFile);

echo "Version bumped to $newVersion\n";
