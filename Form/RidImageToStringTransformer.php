<?php
namespace Rid\Bundle\ImageBundle\Form;

use Rid\Bundle\ImageBundle\Model\RidImage;
use Symfony\Component\Form\DataTransformerInterface;

class RidImageToStringTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return (string) $value;
    }

    public function reverseTransform($value)
    {
        return new RidImage($value);
    }
}
