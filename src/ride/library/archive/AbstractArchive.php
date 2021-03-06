<?php

namespace ride\library\archive;

use ride\library\archive\exception\ArchiveException;
use ride\library\system\file\File;

/**
 * Abstract implementation for an archive
 */
class AbstractArchive implements Archive {

    /**
     * File system to work with
     * @var \ride\library\system\file\FileSystem
     */
    protected $fileSystem;

    /**
     * File of the archive
     * @var \ride\library\system\file\File
     */
    protected $file;

    /**
     * Constructs a new archive
     * @param \ride\library\system\file\File $file The file of the archive
     * @return null
     */
    public function __construct(File $file) {
        $this->file = $file;
        $this->fileSystem = $file->getFileSystem();
    }

    /**
     * Compresses a file or combination of files in the archive
     * @param array|\ride\library\system\file\File $source File or array of
     * files to compress
     * @param string $prefix Path for the files in the archive
     * @return null
     */
    public function compress($source, $prefix = null) {
        throw new ArchiveException('Unsupported action');
    }

    /**
     * Uncompresses the archive to the provided destination. This
     * implementation will always throw an exception, implement it to override.
     * @param \ride\library\system\file\File $destination Destination of the
     * uncompressed files
     * @return null
     */
    public function uncompress(File $destination) {
        throw new ArchiveException('Unsupported action');
    }

}
