<?php

namespace Byss\Bundle\AppBundle\Enrich\Provider\Field;

use Pim\Bundle\EnrichBundle\Provider\Field\FieldProviderInterface;
use Pim\Component\Catalog\Model\AttributeInterface;
use Byss\Bundle\AppBundle\AttributeType\ByssAttributeTypes;

class MultiImageFieldProvider implements FieldProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getField($element)
    {
        return ByssAttributeTypes::MULTI_IMAGE;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($element)
    {
        return $element instanceof AttributeInterface
            && ByssAttributeTypes::MULTI_IMAGE === $element->getType();
    }
}
