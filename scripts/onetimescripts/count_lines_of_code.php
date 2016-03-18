<?php

$date = new DateTime();
echo "\nScript Start Time: " . date_format($date, 'Y-m-d H:i:s'). "\n\n";

$DS = DIRECTORY_SEPARATOR;
$basePath = realpath(dirname(dirname(__DIR__)));
$service_dir = realpath(dirname(dirname(__DIR__))) . "{$DS}service";
$scripts_dir = realpath(dirname(dirname(__DIR__))). "{$DS}scripts";
$js_dir = realpath(dirname(dirname(__DIR__))) . "{$DS}ui{$DS}js";
$html_dir = realpath(dirname(dirname(__DIR__))) . "{$DS}ui{$DS}html";

// Set only one directory at a time here
$directoryPath = $service_dir;
//$directoryPath = $scripts_dir;
//$directoryPath = $js_dir;
//$directoryPath = $html_dir;

// Set one file extension pattern to be matched
$filePattern = '/\.php/';
//$filePattern = '/\.js/';
//$filePattern = '/\.html/';


// Add any directories to skip here
$skippedDirectories = array(
    realpath(dirname(dirname(__DIR__))). "{$DS}scripts{$DS}node",
    realpath(dirname(dirname(__DIR__))). "{$DS}service{$DS}extensions",
    realpath(dirname(dirname(__DIR__))). "{$DS}service{$DS}lib",
    realpath(dirname(dirname(__DIR__))). "{$DS}service{$DS}node",
    realpath(dirname(dirname(__DIR__))). "{$DS}service{$DS}vendor",
    realpath(dirname(dirname(__DIR__))). "{$DS}ui{$DS}js{$DS}libs",
);

$allDirectoriesObject = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryPath));

$directoryArray = array();

foreach($allDirectoriesObject as $oneDirectoryObject) {
    $path = $oneDirectoryObject->getPathName();
    $realPath = realpath($path);

    if ($oneDirectoryObject->isDir()) {
        $trimmedPath = dirname($path);
        $directoryArray[] = $trimmedPath;
    }
}
$cleanedDirectoryArray = array();

foreach($directoryArray as $directory) {
    $remove = false;
    foreach($skippedDirectories as $skippedDirectory) {
        if (strpos($directory, $skippedDirectory) !== false || $directory == $directoryPath) {
            $remove = true;
        }
    }
    if ($remove === false ) {
        $cleanedDirectoryArray[$directory] = true;
    }
}

ksort($cleanedDirectoryArray);

$date = new DateTime();
$displayDate = date_format($date, 'Y-m-d_H-i-s');
$codeCountFile = fopen('./count_lines_of_code'.$displayDate.'.txt', 'w');
$totalLines = 0;
$totalFiles = 0;

$shortBasePath = str_replace($basePath, "", $directoryPath);
fwrite($codeCountFile, "Count Lines of Code for ".$filePattern. " files in ".$shortBasePath.", Runtime: ".$displayDate."\n");

foreach($cleanedDirectoryArray as $oneDirectory => $value) {
    $shortDirectory = str_replace($basePath, "", $oneDirectory);
    echo $oneDirectory."\n";

    $fileCount = 0;

    fwrite($codeCountFile, "\n\nLine Counts in Directory: ".$shortDirectory."\n");
    fwrite($codeCountFile, "================================================================================\n");

    $directory = new RecursiveDirectoryIterator($oneDirectory);
    $iterator = new RecursiveIteratorIterator($directory);

    $totalDirectoryLines = 0;
    $directoryFiles = array();
    foreach($iterator as $fileInfo)
    {
        $lineCount = 0;
        $filename = $fileInfo->getFileName();
        if (preg_match($filePattern, $filename)) {
            $lineCount = count(file($fileInfo));
            $directoryFiles[$filename] = $lineCount;
            $totalDirectoryLines += $lineCount;
            $fileCount++;
        }
    }
    uksort($directoryFiles, 'strcasecmp');
    foreach($directoryFiles as $sortedFilename => $sortedLineCount) {

        fwrite($codeCountFile, $sortedFilename. ": ". number_format($sortedLineCount)."\n");
    }

    $totalLines += $totalDirectoryLines;
    $totalFiles += $fileCount;
    fwrite($codeCountFile, "\nDirectory: ".$shortDirectory."\n");
    fwrite($codeCountFile, "Number of files: ".number_format($fileCount)."\n");
    fwrite($codeCountFile, "Total lines of code: ".number_format($totalDirectoryLines)."\n");
    fwrite($codeCountFile, "________________________________________________________________________________\n\n");
}

fwrite($codeCountFile, "\n\nTotal number of files for all directories in".$shortBasePath.": ".number_format($totalFiles)."\n");
fwrite($codeCountFile, "Total lines of code for all directories in".$shortBasePath.": ".number_format($totalLines)."\n");


fclose($codeCountFile);

$date = new DateTime();
echo "\nScript End Time: " . date_format($date, 'Y-m-d H:i:s'). "\n\n";


function countLinesInFile($fileInfo)
{
    return count(file($fileInfo));
}


?>