<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\html\val_tester;

use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class ValTesterAreaCoords
 * @implements ValTesterInterface<string>
 * coordinates define the placement and extent of the shape which is specified in the area element.  There is a
 * valid shape called default which does not accept any coordinates.
 */
class AreaCoordsTester implements ValTesterInterface
{
    protected string $shape = 'rect';

    /**
     * testValue
     * @param string $value
     * @return bool
     * I don't think browsers will kick out nonsensical numbers like a circle with a diameter of 0 or a
     * rectangle with no height and/or no width
     */
    public function testValue(mixed $value): bool
    {
        $coords = explode(',', $value);
        $callable = function (bool $carry, string $x): bool {
            return $carry && ctype_digit(trim($x));
        };
        if (!array_reduce($coords, $callable, true)) {
            return false;
        }

        /**
         * rectangles take 4 coordinates
         */
        if ($this->getShape() == 'rect' && count($coords) != 4) {
            return false;
        }

        /**
         * circles take 3 coordinates: the first two are the center of the circle, the third is the radius
         */
        if ($this->getShape() == 'circle' && count($coords) != 3) {
            return false;
        }

        /**
         * polygons must have an even number of coordinates greater than 4
         */
        if ($this->getShape() == 'poly' && (count($coords) < 4 || count($coords) % 2 != 0)) {
            return false;
        }

        return true;
    }

    public function getShape(): string
    {
        return $this->shape;
    }

    public function setShape(string $shape): void
    {
        $this->shape = strtolower($shape);
    }
}
