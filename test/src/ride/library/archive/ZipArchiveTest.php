<?php

namespace ride\library\archive;

use ride\library\system\System;

use \PHPUnit_Framework_TestCase;

class ZipArchiveTest extends PHPUnit_Framework_TestCase {

    public function testCompress() {
        $system = new System();
        $fileSystem = $system->getFileSystem();

        $archiveFile = $fileSystem->getTemporaryFile();
        $archive = new ZipArchive($archiveFile);

        // compress single file in the root of the archive
        $file = $fileSystem->getFile(__FILE__);
        $archive->compress($file);

        // compress multiple files in a folder in the archive
        $files = array(
            $fileSystem->getFile(__DIR__ . '/../../../../../composer.json'),
            $fileSystem->getFile(__DIR__ . '/../../../../../README.md'),
        );
        $archive->compress($files, 'path/in/archive');

        $archiveFile->delete();
    }

}
