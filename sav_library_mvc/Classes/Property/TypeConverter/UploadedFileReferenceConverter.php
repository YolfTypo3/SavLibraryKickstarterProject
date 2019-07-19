<?php
namespace YolfTypo3\SavLibraryMvc\Property\TypeConverter;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with TYPO3 source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException;
use TYPO3\CMS\Core\Resource\File as FalFile;
use TYPO3\CMS\Core\Resource\FileReference as FalFileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Property\Exception\TypeConverterException;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;

/**
 * Class UploadedFileReferenceConverter
 *
 * For compatibility with TYPO3 v7
 * @todo This class will be removed in TYPO3 v10
 */
class UploadedFileReferenceConverter extends AbstractTypeConverter
{
    /**
     * Folder where the file upload should go to (including storage).
     */
    const CONFIGURATION_UPLOAD_FOLDER = 1;

    /**
     * How to handle a upload when the name of the uploaded file conflicts.
     */
    const CONFIGURATION_UPLOAD_CONFLICT_MODE = 2;

    /**
     * Whether to replace an already present resource.
     * Useful for "maxitems = 1" fields and properties
     * with no ObjectStorage annotation.
     */
    const CONFIGURATION_ALLOWED_FILE_EXTENSIONS = 4;

    /**
     * @var string
     */
    protected $defaultUploadFolder = '1:/user_upload/';

    /**
     * One of 'cancel', 'replace', 'changeName'
     *
     * @var string
     */
    protected $defaultConflictMode = 'replace';

    /**
     * @var array<string>
     */
    protected $sourceTypes = [
        'array'
    ];

    /**
     * @var string
     */
    protected $targetType = 'TYPO3\\CMS\\Extbase\\Domain\\Model\\FileReference';

    /**
     * Take precedence over the available FileReferenceConverter
     *
     * @var integer
     */
    protected $priority = 2;

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     * @extensionScannerIgnoreLine
     * @inject
     */
    protected $resourceFactory;

    /**
     * @var \TYPO3\CMS\Extbase\Security\Cryptography\HashService
     * @extensionScannerIgnoreLine
     * @inject
     */
    protected $hashService;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @extensionScannerIgnoreLine
     * @inject
     */
    protected $persistenceManager;

    /**
     * @var \TYPO3\CMS\Core\Resource\FileInterface[]
     */
    protected $convertedResources = [];

    /**
     * Actually convert from $source to $targetType, taking into account the fully
     * built $convertedChildProperties and $configuration.
     *
     * @param string|integer $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     * @return \TYPO3\CMS\Extbase\Domain\Model\AbstractFileFolder @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        if (! isset($source['error']) || $source['error'] === \UPLOAD_ERR_NO_FILE) {
            if (isset($source['submittedFile']['resourcePointer'])) {
                try {
                    $resourcePointer = $this->hashService->validateAndStripHmac($source['submittedFile']['resourcePointer']);
                    if (strpos($resourcePointer, 'file:') === 0) {
                        $fileUid = substr($resourcePointer, 5);
                        return $this->createFileReferenceFromFalFileObject($this->resourceFactory->getFileObject($fileUid));
                    } else {
                        return $this->createFileReferenceFromFalFileReferenceObject($this->resourceFactory->getFileReferenceObject($resourcePointer), $resourcePointer);
                    }
                } catch (\InvalidArgumentException $e) {
                    // Nothing to do. No file is uploaded and resource pointer is invalid. Discard!
                }
            }
            // Returns an empty new file to get all posted elements
            return new \TYPO3\CMS\Extbase\Domain\Model\File();
        }

        if ($source['error'] !== \UPLOAD_ERR_OK) {
            switch ($source['error']) {
                case \UPLOAD_ERR_INI_SIZE:
                case \UPLOAD_ERR_FORM_SIZE:
                case \UPLOAD_ERR_PARTIAL:
                    return new Error(Files::getUploadErrorMessage($source['error']), 1264440823);
                default:
                    return new Error('An error occurred while uploading. Please try again or contact the administrator if the problem remains', 1340193849);
            }
        }
        if (isset($this->convertedResources[$source['tmp_name']])) {
            return $this->convertedResources[$source['tmp_name']];
        }
        try {
            $resource = $this->importUploadedResource($source, $configuration);
        } catch (\Exception $e) {
            return new Error($e->getMessage(), $e->getCode());
        }
        $this->convertedResources[$source['tmp_name']] = $resource;
        return $resource;
    }

    /**
     * Import a resource and respect configuration given for properties
     *
     * @param array $uploadInfo
     * @param PropertyMappingConfigurationInterface $configuration
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @throws TypeConverterException
     * @throws ExistingTargetFileNameException
     */
    protected function importUploadedResource(array $uploadInfo, PropertyMappingConfigurationInterface $configuration)
    {
        if (! GeneralUtility::verifyFilenameAgainstDenyPattern($uploadInfo['name'])) {
            throw new TypeConverterException('Uploading files with PHP file extensions is not allowed!', 1399312430);
        }
        $allowedFileExtensions = $configuration->getConfigurationValue(\YolfTypo3\SavLibraryMvc\Property\TypeConverter\UploadedFileReferenceConverter::class, self::CONFIGURATION_ALLOWED_FILE_EXTENSIONS);
        if ($allowedFileExtensions !== null) {
            $filePathInfo = PathUtility::pathinfo($uploadInfo['name']);
            if (! GeneralUtility::inList($allowedFileExtensions, strtolower($filePathInfo['extension']))) {
                throw new TypeConverterException('File extension is not allowed!', 1399312430);
            }
        }
        $uploadFolderId = $configuration->getConfigurationValue(\YolfTypo3\SavLibraryMvc\Property\TypeConverter\UploadedFileReferenceConverter::class, self::CONFIGURATION_UPLOAD_FOLDER) ?  : $this->defaultUploadFolder;
        $conflictMode = $configuration->getConfigurationValue(\YolfTypo3\SavLibraryMvc\Property\TypeConverter\UploadedFileReferenceConverter::class, self::CONFIGURATION_UPLOAD_CONFLICT_MODE) ?  : $this->defaultConflictMode;
        $uploadFolder = $this->resourceFactory->retrieveFileOrFolderObject($uploadFolderId);
        $uploadedFile = $uploadFolder->addUploadedFile($uploadInfo, $conflictMode);
        $resourcePointer = isset($uploadInfo['submittedFile']['resourcePointer']) && strpos($uploadInfo['submittedFile']['resourcePointer'], 'file:') === false ? $this->hashService->validateAndStripHmac($uploadInfo['submittedFile']['resourcePointer']) : null;
        $fileReferenceModel = $this->createFileReferenceFromFalFileObject($uploadedFile, $resourcePointer);
        return $fileReferenceModel;
    }

    /**
     * @param FalFile $file
     * @param int $resourcePointer
     * @return \YolfTypo3\SavLibraryMvc\Domain\Model\DefaultModel
     */
    protected function createFileReferenceFromFalFileObject(FalFile $file, $resourcePointer = null)
    {
        $fileReference = $this->resourceFactory->createFileReferenceObject([
                'uid_local' => $file->getUid(),
                'uid_foreign' => uniqid('NEW_'),
                'uid' => uniqid('NEW_'),
                'crop' => null
            ]
        );
        return $this->createFileReferenceFromFalFileReferenceObject($fileReference, $resourcePointer);
    }

    /**
     * @param FalFileReference $falFileReference
     * @param int $resourcePointer
     * @return \YolfTypo3\SavLibraryMvc\Domain\Model\DefaultModel
     */
    protected function createFileReferenceFromFalFileReferenceObject(FalFileReference $falFileReference, $resourcePointer = null)
    {
        if ($resourcePointer === null) {
            /**
             * @var $fileReference \YolfTypo3\SavLibraryMvc\Domain\Model\DefaultModel
             */
            $fileReference = $this->objectManager->get(\TYPO3\CMS\Extbase\Domain\Model\FileReference::class);
        } else {
            $fileReference = $this->persistenceManager->getObjectByIdentifier($resourcePointer, \TYPO3\CMS\Extbase\Domain\Model\FileReference::class, false);
        }
        $fileReference->setOriginalResource($falFileReference);
        return $fileReference;
    }

}
