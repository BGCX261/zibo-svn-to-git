<?php

namespace zibo\library\archive;

use zibo\library\filesystem\File;

/**
 * Interface for a file archive
 */
interface Archive {

    /**
     * Constructs a new archive
     * @param zibo\library\filesystem\File $file File of the archive
     * @return null
     */
    public function __construct(File $archive);

    /**
     * Compresses a file or combination of files in the archive
     * @param array|zibo\library\filesystem\File $source The source(s) of the file(s) to compress
     * @param zibo\library\filesystem\File $prefix The path for the files in the archive
     * @return null
     */
    public function compress($source, File $prefix = null);

    /**
     * Uncompresses the archive to the provided destination
     * @param zibo\library\filesystem\File $destination Destination of the uncompressed files
     * @return null
     */
    public function uncompress(File $destination);

}