<?php

namespace pvc\html\factory;

use pvc\htmlbuilder\definitions\types\DefinitionType;

class DefIdResolver
{
    /**
     * there are several identifiers in html which are duplicates, i.e. out of context, you would not know whether
     * you are referring to an attribute or an element.  For this reason and because we are using a single container,
     * there are some cases where the definition id needs to be different from the name of the object.  The
     * method of disambiguation is to append an _attr or _element to the names of the objects and make those the
     * definition ids.  For example, cite => cite_attr / cite_element.
     *
     * The ambiguous identifiers are:
     *
     * cite
     * data
     * form
     * label
     * span
     * style
     * title
     *
     * @var array<string>
     */
    private static array $ambiguousIdentifiers = [
        'cite',
        'data',
        'form',
        'label',
        'span',
        'style',
        'title',
        'type',
    ];

    /**
     * @var string
     */
    private static string $attributeSuffix = '_attr';

    /**
     * @var string
     */
    private static string $elementSuffix = '_element';

    /**
     * @param  string  $name
     * @return bool
     */
    protected static function isAmbiguousName(string $name): bool
    {
        return(in_array($name, self::$ambiguousIdentifiers));
    }

    /**
     * @param  string  $name
     * @param  DefinitionType  $defType
     *
     * @return string
     */
    protected static function getSuffix(DefinitionType $defType): string
    {
        return ($defType === DefinitionType::Attribute) ?
            self::$attributeSuffix :
            self::$elementSuffix;
    }

    /**
     * @param  string  $name
     * @param  DefinitionType  $defType
     *
     * @return string
     */
    public static function getDefIdFromName(string $name, DefinitionType $defType): string
    {
        $suffix = self::isAmbiguousName($name) ?
            self::getSuffix($defType) :
            '';
        return $name . $suffix;
    }
}
