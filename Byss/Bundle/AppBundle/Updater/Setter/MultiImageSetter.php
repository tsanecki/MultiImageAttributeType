<?php

namespace Byss\Bundle\AppBundle\Updater\Setter;

use Akeneo\Component\FileStorage\File\FileStorerInterface;
use Akeneo\Component\FileStorage\Model\FileInfoInterface;
use Akeneo\Component\FileStorage\Repository\FileInfoRepositoryInterface;
use Akeneo\Component\StorageUtils\Exception\InvalidPropertyException;
use Akeneo\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Pim\Component\Catalog\Builder\EntityWithValuesBuilderInterface;
use Pim\Component\Catalog\FileStorage;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\EntityWithValuesInterface;
use Pim\Component\Catalog\Updater\Setter\AbstractAttributeSetter;

/**
 * Sets multi image field in a product.
 *
 * @author    Tomasz Sanecki
 * @copyright 2019 Byss SC
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MultiImageSetter extends AbstractAttributeSetter
{
    /** @var FileStorerInterface */
    protected $storer;

    /** @var FileInfoRepositoryInterface */
    protected $repository;

    /**
     * @param EntityWithValuesBuilderInterface $entityWithValuesBuilder
     * @param FileStorerInterface              $storer
     * @param FileInfoRepositoryInterface      $repository
     * @param string[]                         $supportedTypes
     */
    public function __construct(
        EntityWithValuesBuilderInterface $entityWithValuesBuilder,
        FileStorerInterface $storer,
        FileInfoRepositoryInterface $repository,
        array $supportedTypes
    ) {
        parent::__construct($entityWithValuesBuilder);
        $this->storer = $storer;
        $this->repository = $repository;
        $this->supportedTypes = $supportedTypes;
    }

    /**
     * {@inheritdoc}
     *
     * Expected data input format :  "/absolute/file/path/filename.extension"
     */
    public function setAttributeData(
        EntityWithValuesInterface $entityWithValues,
        AttributeInterface $attribute,
        $data,
        array $options = []
    ) {

        $options = $this->resolver->resolve($options);
        $this->checkData($attribute, $data);

        $files = [];

        if ((null === $data) || ("" === $data)) {
            $files = null;
        } else {
            if (!is_array($data)) {
                $data = [$data];
            }

            foreach ($data as $d) {
                if (null === $file = $this->repository->findOneByIdentifier($d)) {
                    $file = $this->storeFile($attribute, $d);
                }
                $files[] = $file->getKey();
            }
        }

        $this->entityWithValuesBuilder->addOrReplaceValue(
            $entityWithValues,
            $attribute,
            $options['locale'],
            $options['scope'],
            null !== $files ? $files : null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute(AttributeInterface $attribute)
    {
        return 'byssapp_catalog_multi_image' === $attribute->getType();
    }

    /**
     * @param AttributeInterface $attribute
     * @param mixed              $data
     *
     * @throws InvalidPropertyTypeException
     */
    protected function checkData(AttributeInterface $attribute, $data)
    {
        return 1;
    }

    protected function storeFile(AttributeInterface $attribute, $data)
    {
        if (null === $data) {
            return null;
        }

        $rawFile = new \SplFileInfo($data['filePath']);

        if (!$rawFile->isFile()) {
            throw InvalidPropertyException::validPathExpected(
                $attribute->getCode(),
                static::class,
                $data
            );
        }

        $file = $this->storer->store($rawFile, FileStorage::CATALOG_STORAGE_ALIAS);

        return $file;
    }
}
