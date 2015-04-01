# Ride: Archive Library

Ride library for abstraction of file archiving.

## Code Sample

Check this code sample to see how to use this library:

    <?php

    use ride\library\archive\ZipArchive;    
    use ride\library\system\file\FileSystem;    
    
    function createArchive(FileSystem $fileSystem) {
        // dummy files to compress
        $files = array(
            $fileSystem->getFile('/my/file'),
            $fileSystem->getFile('/my/second-file'),
        );
        $file = $fileSystem->getFile('/my/third-file');
            
        // create the archive
        $archiveFile = $fileSystem->getTemporaryFile();
        $archive = new ZipArchive($archiveFile);
        
        // compress single file in the root of the archive
        $archive->compress($file);
        
        // compress multiple files in a folder in the archive
        $path = $fileSystem->getFile('path/in/archive');
        $archive->compress($files, $path); 
    
        // we're done here
        return $archiveFile;
    }
