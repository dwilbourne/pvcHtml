<?php
declare(strict_types=1);
namespace pvc\html\elements;
use pvc\html\element\Element;
use pvc\html\factory\HtmlFactory;
class A extends Element
{
use \pvc\html\attributes\DownloadTrait;
use \pvc\html\attributes\HrefTrait;
use \pvc\html\attributes\HreflangTrait;
use \pvc\html\attributes\MediaTrait;
use \pvc\html\attributes\PingTrait;
use \pvc\html\attributes\ReferrerpolicyTrait;
use \pvc\html\attributes\RelTrait;
use \pvc\html\attributes\TargetTrait;
public function __construct(
string $name,
array $attributeObjects,
array $elementObjects,
HtmlFactory $htmlFactory)
{
parent::__construct($name, $attributeObjects, $elementObjects,
$htmlFactory);
}
}
