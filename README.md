# Ride: Archive Library

Ride library for abstraction of file archiving.

## Code Sample

Check this code sample to see how to use this library:

```php
<?php

use ride\library\archive\ZipArchive;    
use ride\library\system\file\FileSystem;    

function createArchive(FileSystem $fileSystem) {          
    // create the archive
    $archiveFile = $fileSystem->getTemporaryFile();
    $archive = new ZipArchive($archiveFile);
    
    // compress single file in the root of the archive
    $file = $fileSystem->getFile('/my/file');
    $archive->compress($file);
    
    // compress multiple files in a folder in the archive
    $files = array(
        $fileSystem->getFile('/my/second-file'),
        $fileSystem->getFile('/my/third-file'),
    );
    $archive->compress($files, 'path/in/archive'); 

    // we're done here
    return $archiveFile;
}
```
