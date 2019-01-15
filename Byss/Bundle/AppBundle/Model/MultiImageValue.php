<?php

namespace Byss\Bundle\AppBundle\Model;

use Pim\Component\Catalog\Model\AbstractValue;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ValueInterface;

/**
 * Product value for MultiImage atribute type
 *
 * @author    Tomasz Sanecki <t.sanecki@byss.pl>
 * @copyright 2018 Byss
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MultiImageValue extends AbstractValue implements ValueInterface
{
    /** @var string[] */
    protected $data;

    /**
     * @param AttributeInterface $attribute
     * @param string             $channel
     * @param string             $locale
     * @param mixed              $data
     */
    public function __construct(AttributeInterface $attribute, $channel, $locale, $data)
    {
        $this->setAttribute($attribute);
        $this->setScope($channel);
        $this->setLocale($locale);

        $this->data = $data;
    }

    /**
     * @return string[]
     */
    public function getData()
    {
        $data = $this->data;
        if (is_string($data)) {
            trim($data);
            if (substr($data,0,1) == '"'
                && substr($data, strlen($data) - 1, 1) == '"') {
                $data = substr($data, 1, strlen($data) - 2);
            }
            $d = json_decode($data);
            if (json_last_error() === 0) {
                $data = $d;
            }
        }

        return $data;
    }

    /**
     * @param string $item
     */
    public function removeItem(string $item)
    {
        $data = array_filter($this->data, function ($value) use ($item) {
            return $value !== $item;
        });
        $this->data = array_values($data);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return implode(', ', $this->data);
    }
}
