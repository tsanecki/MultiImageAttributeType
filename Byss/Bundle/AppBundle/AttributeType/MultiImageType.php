<?php

namespace Byss\Bundle\AppBundle\AttributeType;
use Pim\Bundle\CatalogBundle\AttributeType\AbstractAttributeType;

/**
 * Multi Image attribute type
 *
 * @author    Tomasz Sanecki <t.sanecki@byss.pl>
 * @copyright 2017 Byss
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MultiImageType extends AbstractAttributeType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return ByssAttributeTypes::MULTI_IMAGE;
    }
}
