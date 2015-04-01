<?php

namespace ride\library\archive;

use ride\library\system\file\File;

/**
 * Interface for a file archive
 */
interface Archive {

    /**
     * Constructs a new archive
     * @param \ride\library\system\file\File $file File of the archive
     * @return null
     */
    public function __construct(File $file);

    /**
     * Compresses a file or combination of files in the archive
     * @param array|\ride\library\system\file\File $source File or array of
     * files to compress
     * @param string $prefix Path for the files in the archive
     * @return null
     */
    public function compress($source, $prefix = null);

    /**
     * Uncompresses the archive to the provided destination
     * @param \ride\library\system\file\File $destination Destination for the
     * uncompressed files
     * @return null
     */
    public function uncompress(File $destination);

}
