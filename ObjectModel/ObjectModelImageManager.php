<?php
/**
 *  NOTICE OF LICENSE
 * 
 * This file is licensed under the Software License Agreement.
 * 
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 * 
 * You must not modify, adapt or create derivative works of this source code
 * 
 * @author Arkonsoft
 * @copyright 2025 Arkonsoft
 */

declare(strict_types=1);

namespace Arkonsoft\PsModule\Core\ObjectModel;

use Exception;

class ImageManagerException extends Exception {}

if(!defined('_PS_VERSION_')) {
    exit;
}

class ObjectModelImageManager
{
    const DEFAULT_ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const DEFAULT_ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @var array
     */
    protected $allowedExtensions;

    /**
     * @var array
     */
    protected $allowedMimeTypes;

    /**
     * @param string $moduleName Module name
     * @param array $allowedExtensions List of allowed file extensions
     * @param array $allowedMimeTypes List of allowed MIME types
     * @throws ImageManagerException
     */
    public function __construct(
        string $moduleName,
        array $allowedExtensions = self::DEFAULT_ALLOWED_EXTENSIONS,
        array $allowedMimeTypes = self::DEFAULT_ALLOWED_MIME_TYPES
    ) {
        $this->moduleName = $moduleName;
        $this->allowedExtensions = $allowedExtensions;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->validateConstructorParams();
    }

    /**
     * Returns the directory path where images will be stored
     * 
     * @return string Directory path for image storage
     */
    protected function getImageDestinationDir(): string
    {
        return _PS_IMG_DIR_ . 'modules/' . $this->moduleName . '/';
    }

    /**
     * Returns the URI path for accessing images
     * 
     * @return string URI path for image access
     */
    protected function getImageDestinationUri(): string
    {
        return _PS_IMG_ . 'modules/' . $this->moduleName . '/';
    }

    /**
     * Saves an uploaded image in multiple formats
     * 
     * @param string $fieldName Name of the file input field
     * @param int $objectId ID of the object the image belongs to
     * @param string $type Type of the image
     * @param array $extensions List of output formats to generate
     * @param int|null $width Width of the image
     * @param int|null $height Height of the image
     * @throws ImageManagerException
     */
    public function saveImage($fieldName, $objectId, $type, array $extensions, $width = null, $height = null)
    {
        if (!$this->isFileUploaded($fieldName)) {
            return;
        }

        $this->validateUploadedFile($fieldName);
        $this->ensureDestinationDirectoryExists();

        foreach ($extensions as $extension) {
            $this->validateOutputExtension($extension);
            $this->saveImageInFormat($fieldName, $objectId, $type, $extension, $width, $height);
        }
    }

    /**
     * Deletes an image in all specified formats
     * 
     * @param int $objectId ID of the object the image belongs to
     * @param string $type Type of the image
     * @param array $extensions List of formats to delete
     */
    public function deleteImage($objectId, $type)
    {
        $files = glob($this->getImageDestinationDir() . $objectId . '_' . $type . '.*');
        
        if (!empty($files)) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Validates constructor parameters
     * 
     * @throws ImageManagerException When allowed extensions or MIME types are empty
     */
    protected function validateConstructorParams()
    {
        if (empty($this->allowedExtensions)) {
            throw new ImageManagerException('Allowed extensions list cannot be empty');
        }

        if (empty($this->allowedMimeTypes)) {
            throw new ImageManagerException('Allowed MIME types list cannot be empty');
        }
    }

    /**
     * Checks if a file was uploaded for the given field name
     * 
     * @param string $fieldName Name of the file input field
     * @return bool True if file was uploaded, false otherwise
     */
    protected function isFileUploaded($fieldName)
    {
        return isset($_FILES[$fieldName]) && !empty($_FILES[$fieldName]['name']);
    }

    /**
     * @param string $fieldName
     * @throws ImageManagerException
     */
    protected function validateUploadedFile($fieldName)
    {
        if (!isset($_FILES[$fieldName]['error']) || is_array($_FILES[$fieldName]['error'])) {
            throw new ImageManagerException('Invalid file upload parameters');
        }

        // Check for upload errors
        switch ($_FILES[$fieldName]['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new ImageManagerException('The uploaded file exceeds the maximum allowed size');
            case UPLOAD_ERR_PARTIAL:
                throw new ImageManagerException('The file was only partially uploaded');
            case UPLOAD_ERR_NO_FILE:
                throw new ImageManagerException('No file was uploaded');
            case UPLOAD_ERR_NO_TMP_DIR:
                throw new ImageManagerException('Missing a temporary folder');
            case UPLOAD_ERR_CANT_WRITE:
                throw new ImageManagerException('Failed to write file to disk');
            case UPLOAD_ERR_EXTENSION:
                throw new ImageManagerException('A PHP extension stopped the file upload');
            default:
                throw new ImageManagerException('Unknown upload error');
        }

        // Validate file extension
        $uploadedFileExtension = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
        if (!in_array($uploadedFileExtension, $this->allowedExtensions)) {
            throw new ImageManagerException('Invalid file extension. Allowed extensions: ' . implode(', ', $this->allowedExtensions));
        }

        // Validate mime type
        $mimeType = mime_content_type($_FILES[$fieldName]['tmp_name']);
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            throw new ImageManagerException('Invalid file type. Allowed types: ' . implode(', ', $this->allowedMimeTypes));
        }

        // Additional security check - verify the file is actually an image
        if (!getimagesize($_FILES[$fieldName]['tmp_name'])) {
            throw new ImageManagerException('The uploaded file is not a valid image');
        }
    }

    /**
     * Ensures that the destination directory exists and is writable
     * 
     * @throws ImageManagerException When directory cannot be created or is not writable
     */
    protected function ensureDestinationDirectoryExists()
    {
        if (!is_dir($this->getImageDestinationDir())) {
            if (!mkdir($this->getImageDestinationDir(), 0777, true)) {
                throw new ImageManagerException('Unable to create image directory');
            }
        }
        
        if (!is_writable($this->getImageDestinationDir())) {
            throw new ImageManagerException('Image directory is not writable');
        }
    }

    /**
     * Validates the output extension against allowed extensions
     * 
     * @param string $extension File extension to validate
     * @throws ImageManagerException When extension is not allowed
     */
    protected function validateOutputExtension($extension)
    {
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new ImageManagerException('Invalid output extension: ' . $extension);
        }
    }

    /**
     * Saves an image in a specific format
     * 
     * @param string $fieldName Name of the file input field
     * @param int $objectId ID of the object the image belongs to
     * @param string $type Type of the image
     * @param string $extension Output format extension
     * @param int|null $width Width of the image
     * @param int|null $height Height of the image
     * @throws ImageManagerException When image upload fails
     */
    protected function saveImageInFormat($fieldName, $objectId, $type, $extension, $width = null, $height = null)
    {
        $filename = $this->generateFilename($objectId, $type, $extension);
        $destination = $this->getImageDestinationDir() . $filename;

        // Get image dimensions from the temporary file
        list($originalWidth, $originalHeight) = getimagesize($_FILES[$fieldName]['tmp_name']);

        // Calculate missing dimension if only one is provided
        if ($width !== null && $height === null) {
            $height = $this->calculateProportionalHeight($originalWidth, $originalHeight, $width);
        } elseif ($height !== null && $width === null) {
            $width = $this->calculateProportionalWidth($originalWidth, $originalHeight, $height);
        }

        // Prevent image upscaling
        if ($width !== null && $originalWidth < $width) {
            $width = $originalWidth;
            if ($height !== null) {
                $height = $this->calculateProportionalHeight($originalWidth, $originalHeight, $width);
            }
        }

        if ($height !== null && $originalHeight < $height) {
            $height = $originalHeight;
            if ($width !== null) {
                $width = $this->calculateProportionalWidth($originalWidth, $originalHeight, $height);
            }
        }

        $uploadResult = \ImageManager::resize(
            $_FILES[$fieldName]['tmp_name'],
            $destination,
            $width,
            $height,
            $extension,
            true
        );

        if (!$uploadResult) {
            throw new ImageManagerException('An error occurred while uploading the image');
        }
    }

    /**
     * Calculates the proportional height for an image
     * 
     * @param int $originalWidth Original width of the image
     * @param int $originalHeight Original height of the image
     * @param int $targetWidth Target width for the image
     * @return int Proportional height
     */
    protected function calculateProportionalHeight(int $originalWidth, int $originalHeight, int $targetWidth): int
    {
        return (int) round($targetWidth * ($originalHeight / $originalWidth));
    }

    /**
     * Calculates the proportional width for an image
     * 
     * @param int $originalWidth Original width of the image
     * @param int $originalHeight Original height of the image
     * @param int $targetHeight Target height for the image
     * @return int Proportional width
     */
    protected function calculateProportionalWidth(int $originalWidth, int $originalHeight, int $targetHeight): int
    {
        return (int) round($targetHeight * ($originalWidth / $originalHeight));
    }
    

    /**
     * Generates a filename for the image
     * 
     * @param int $objectId ID of the object the image belongs to
     * @param string $type Type of the image
     * @param string $extension File extension
     * @return string Generated filename
     */
    protected function generateFilename($objectId, $type, $extension)
    {
        return $objectId . '_' . $type . '.' . $extension;
    }

    /**
     * Generates HTML for displaying a thumbnail image
     * 
     * @param int $objectId ID of the object the image belongs to
     * @param string $type Type of the image
     * @param string $extension File extension
     * @param string $widthInPx Maximum width of the thumbnail in pixels
     * @return string HTML code for the thumbnail or empty string if image doesn't exist
     */
    public function getThumbnailHtml(int $objectId, string $type, string $extension, string $widthInPx = '200px'): string
    {
        $filename = $this->generateFilename($objectId, $type, $extension);
        $imagePath = $this->getImageDestinationDir() . $filename;
        
        if (!file_exists($imagePath)) {
            return '';
        }
        
        $imageUrl = $this->getImageDestinationUri() . $filename;
        
        return '<img src="' . $imageUrl . '" style="max-width: ' . $widthInPx . '; margin: 10px 0;" />';
    }
}