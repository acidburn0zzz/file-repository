<?php

namespace Controllers\Upload;

use Actions\Upload\UploadByHttpActionHandler;
use Controllers\AbstractBaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP/HTTPS handler
 * ==================
 *
 * @package Controllers\Upload
 */
class UploadController extends AbstractBaseController implements UploadControllerInterface
{
    /** @var bool $strictUploadMode */
    private $strictUploadMode = true;

    /** @var array $allowedMimeTypes */
    private $allowedMimeTypes = [];

    /**
     * @return JsonResponse|Response
     */
    public function uploadAction() : Response
    {
        $action = new UploadByHttpActionHandler(
            $this->getContainer()->offsetGet('storage.filesize'),
            $this->getContainer()->offsetGet('storage.allowed_types'),
            $this->getContainer()->offsetGet('manager.storage'),
            $this->getContainer()->offsetGet('manager.file_registry'),
            $this->getContainer()->offsetGet('repository.file'),
            $this->getContainer()->offsetGet('manager.tag')
        );

        $action->setData(
            (string)$this->getRequest()->get('file_name'),
            (bool)$this->getRequest()->get('file_overwrite'),
            array_filter((array)$this->getRequest()->get('tags'))
        );

        $action->setStrictUploadMode($this->isStrictUploadMode());
        $action->setAllowedMimes($this->allowedMimeTypes);

        return new JsonResponse($action->execute());
    }

    /**
     * @inheritdoc
     */
    public function supportsProtocol(string $protocolName) : bool
    {
        return in_array($protocolName, ['http', 'https']);
    }

    /**
     * @param boolean $strictUploadMode
     * @return UploadController
     */
    public function setStrictUploadMode(bool $strictUploadMode): UploadController
    {
        $this->strictUploadMode = $strictUploadMode;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isStrictUploadMode(): bool
    {
        return $this->strictUploadMode;
    }

    /**
     * @param array $allowedMimeTypes
     * @return UploadController
     */
    public function setAllowedMimeTypes(array $allowedMimeTypes)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        return $this;
    }
}