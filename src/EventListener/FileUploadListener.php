<?php

namespace App\EventListener;

use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Handler\UploadHandler;

class FileUploadListener
{

    public function __construct(
        private UploadHandler $uploadHandler
    ) {
    }

    public function onVichUploaderPreUpload(Event $event): void
    {
        $this->uploadHandler->remove($event->getObject(), $event->getMapping()->getFilePropertyName());
    }
}
