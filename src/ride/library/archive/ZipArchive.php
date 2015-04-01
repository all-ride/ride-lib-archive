<?php

namespace ride\library\archive;

use ride\library\archive\exception\ArchiveException;
use ride\library\system\file\File;

use \ZipArchive as PhpZipArchive;

/**
 * Zip archive implementation
 */
class ZipArchive extends AbstractArchive {

    /**
     * Constructs a new zip archive
     * @param \ride\library\system\file\File $file The file of the archive
     * @return null
     * @throws \ride\library\archive\exception\ArchiveException when the zip
     * extension is not installed
     */
    public function __construct(File $file) {
        if (!class_exists('ZipArchive')) {
            throw new ArchiveException('Zip is unsupported on this system');
        }

        parent::__construct($file);
    }


    /**
     * Compresses a file or combination of files in the archive
     * @param array|\ride\library\system\file\File $source File or array of
     * files to compress
     * @param string $prefix Path for the files in the archive
     * @return null
     * @throws \ride\library\archive\exception\ArchiveException when no source
     * or an invalid source has been provided
     * @throws \ride\library\archive\exception\ArchiveException when the archive
     * could not be created
     */
    public function compress($source, $prefix = null) {
        if (empty($source)) {
            throw new ArchiveException('No files provided');
        }

        $path = $this->file->getAbsolutePath();

        $parent = $this->file->getParent();
        $parent->create();

        $zip = new PhpZipArchive();
        if ($zip->open($path, PhpZipArchive::CREATE) !== true) {
            throw new ArchiveException('Could not create ' . $path);
        }

        if (!is_array($source)) {
            $source = array($source);
        }

        foreach ($source as $file) {
            if (!($file instanceof File)) {
                throw new ArchiveException('Invalid source provided: ' . $file);
            }

            $this->compressFile($zip, $file, $prefix);
        }

        $zip->close();
    }

    /**
     * Compresses a file into the archive
     * @param \PhpZipArchive $archive PhpZipArchive object of PHP
     * @param \ride\library\system\file\File $file The file to compress in the
     * archive
     * @param string $prefix path for the file in the archive
     * @return null
     */
    private function compressFile(PhpZipArchive $archive, File $file, $prefix = null) {
        if ($prefix) {
            $prefixedFileName = ltrim(rtrim($prefix, '/') . '/' . $file->getName(), '/');
        } else {
            $prefixedFileName = $file->getName();
        }

        $children = null;

        if ($file->exists()) {
            if ($file->isDirectory()) {
                $children = $file->read();
            } else {
                $archive->addFile($file->getPath(), $prefixedFileName);

                return;
            }
        }

        if (empty($children)) {
            $archive->addEmptyDir($prefix);
        } else {
            foreach ($children as $file) {
                $this->compressFile($archive, $file, $prefixedFileName);
            }
        }
    }

    /**
     * Uncompresses the archive to the provided destination
     * @param \ride\library\system\file\File $destination Destination of the
     * uncompressed files
     * @return null
     */
    public function uncompress(File $destination) {
        $path = $this->file->getAbsolutePath();

        $zip = new PhpZipArchive();
        if ($zip->open($path) !== true) {
            throw new ArchiveException('Could not open ' . $path);
        }

        $destination->create();

        $zip->extractTo($destination->getAbsolutePath());
        $zip->close();
    }

}
