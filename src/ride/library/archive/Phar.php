<?php

namespace ride\library\archive;

use ride\library\archive\exception\ArchiveException;
use ride\library\system\file\File;

use \Phar as PhpPhar;
use \UnexpectedValueException;

/**
 * Phar archive implementation
 */
class Phar extends AbstractArchive {

    /**
     * Compresses a file or combination of files in the archive
     * @param array|ride\library\system\file\File $source File objects of the
     * files to compress
     * @param ride\library\system\file\File $prefix The path for the files in
     * the archive
     * @return null
     * @throws \ride\library\archive\exception\ArchiveException when no source
     * or an invalid source has been provided
     * @throws \ride\library\archive\exception\ArchiveException when the archive
     * could not be created
     * @throws \ride\library\archive\exception\ArchiveException when the phars
     * could not be written due to the configuration of PHP
     */
    public function compress($source, File $prefix = null) {
        if (!PhpPhar::canWrite()) {
            throw new ArchiveException('Phar library is not allowed to write phars. Check the PHP configuration for the phar.readonly setting.');
        }

        if (empty($source)) {
            throw new ArchiveException('No files provided');
        }

        $path = $this->file->getAbsolutePath();

        $parent = $this->file->getParent();
        $parent->create();

        if (!is_array($source)) {
            $source = array($source);
        }

        try {
            $phar = new PhpPhar($path);
        } catch (UnexpectedValueException $e) {
            throw new ArchiveException('Could not open ' . $path);
        }

        if (!$phar->isWritable()) {
            throw new ArchiveException('Archive ' . $this->file->getAbsolutePath() . ' is not writable');
        }

        $phar->startBuffering();

        foreach ($source as $file) {
            if (!($file instanceof File)) {
                throw new ArchiveException('Invalid source provided: ' . $file);
            }

            $this->compressFile($phar, $file, $prefix);
        }

        $phar->stopBuffering();
    }

    /**
     * Compresses a file into the archive
     * @param \Phar $archive Phar object of PHP
     * @param \ride\library\system\file\File $file The file to compress in the
     * archive
     * @param \ride\library\system\file\File $prefix The path for the file in
     * the archive
     * @return null
     */
    private function compressFile(PhpPhar $archive, File $file, File $prefix = null) {
        if ($prefix == null) {
            $prefix = new File($file->getName());
        } else {
            $prefix = new File($prefix, $file->getName());
        }

        if ($file->exists()) {
            if ($file->isDirectory()) {
                $this->compressDirectory($archive, $file, $prefix);
            } else {
                $archive->addFile($file->getAbsolutePath(), $prefix->getPath());
            }
        } else {
            throw new ArchiveException("Source does not exist: $file");
        }
    }

    /**
     * Compresses a directory into the archive
     * @param \Phar $archive Phar object of PHP
     * @param \ride\library\system\file\File $dir The directory to compress in
     * the archive
     * @param \ride\library\system\file\File $prefix The path for the directory
     * in the archive
     * @return null
     */
    private function compressDirectory(PhpPhar $archive, File $dir, File $prefix) {
        $children = $dir->read();

        if (empty($children)) {
            $archive->addEmptyDir(new File($prefix->getPath(), $dir->getName()));
        } else {
            foreach ($children as $file) {
                $this->compressFile($archive, $file, $prefix);
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

        try {
            $phar = new PhpPhar($path);
        } catch (UnexpectedValueException $e) {
            throw new ArchiveException('Could not open ' . $path);
        }

        $this->extract($this->file, $destination);
    }

    /**
     * Extracts a source to destination
     * @param \ride\library\system\file\File $source The source phar or directory
     * @param \ride\library\system\file\File $destination The destination
     * directory
     * @return null
     */
    private function extract(File $source, File $destination) {
        $destination->create();

        $files = $source->read();
        if (!$files) {
            return;
        }

        foreach ($files as $sourceFile) {
            $destinationFile = new File($destination, $sourceFile->getName());

            if ($sourceFile->isDirectory()) {
                $this->extract($sourceFile, $destinationFile);
            } else {
                $destinationFile->write($sourceFile->read());
            }
        }
    }

}
