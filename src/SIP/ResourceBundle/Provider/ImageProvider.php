<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\ResourceBundle\Provider;

use Gaufrette\Filesystem;
use Imagine\Image\ImagineInterface;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sonata\MediaBundle\Provider\ImageProvider as BaseImageProvider;

class ImageProvider extends BaseImageProvider implements ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    protected function doTransform(MediaInterface $media)
    {
        parent::doTransform($media);

        if (!is_object($media->getBinaryContent()) && !$media->getBinaryContent()) {
            return;
        }

        if ($this->container->hasParameter('sip_watermark_image')) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $binaryContent */
            $binaryContent = $media->getBinaryContent();

            $watermark = $this->imagineAdapter->open($this->container->getParameter('sip_watermark_image'));
            $newFormat = "{$binaryContent->getPath()}/". uniqid() . ".{$binaryContent->getClientOriginalExtension()}";
            $image     = $this->imagineAdapter->open($binaryContent->getPathname());
            $size      = $image->getSize();
            $wSize     = $watermark->getSize();

            if ($wSize->getWidth() > $size->getWidth()) {
                $ratio = $size->getWidth()/$wSize->getWidth();
                $watermark->getSize()->scale($ratio);
                $watermark->resize($watermark->getSize()->scale($ratio));
            }

            $centerRight = new \Imagine\Image\Point(0, $size->getHeight()/2);
            $image->paste($watermark, $centerRight);
            $image->save($newFormat);

            $newUploadFIle = new UploadedFile($newFormat, $binaryContent->getClientOriginalName());
            $media->setBinaryContent($newUploadFIle);
            $media->setProviderReference($media->getBinaryContent());
        }

        try {
            $image = $this->imagineAdapter->open($media->getBinaryContent()->getPathname());
        } catch (\RuntimeException $e) {
            $media->setProviderStatus(MediaInterface::STATUS_ERROR);

            return;
        }

        $size  = $image->getSize();

        $media->setWidth($size->getWidth());
        $media->setHeight($size->getHeight());

        $media->setProviderStatus(MediaInterface::STATUS_OK);
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}