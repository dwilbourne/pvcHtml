<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\frmtr;

use pvc\interfaces\frmtr\html\FrmtrHtmlInterface;
use pvc\interfaces\frmtr\msg\FrmtrMsgInterface;
use pvc\interfaces\html\factory\definitions\DefinitionFactoryInterface;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * Class FrmtrHtml
 * @template VendorSpecificDefinition of DefinitionFactoryInterface
 * @implements FrmtrHtmlInterface<VendorSpecificDefinition>
 */
class FrmtrHtml implements FrmtrHtmlInterface
{
    protected FrmtrMsgInterface $msgFrmtr;

    public function __construct(FrmtrMsgInterface $msgFrmtr)
    {
        $this->setMsgFrmtr($msgFrmtr);
    }

    /**
     * getLocale
     * @return LocaleInterface
     */
    public function getLocale(): LocaleInterface
    {
        return $this->getMsgFrmtr()->getLocale();
    }

    /**
     * getMsgFrmtr
     * @return FrmtrMsgInterface
     */
    public function getMsgFrmtr(): FrmtrMsgInterface
    {
        return $this->msgFrmtr;
    }

    /**
     * setMsgFrmtr
     * @param FrmtrMsgInterface $frmtrMsg
     */
    public function setMsgFrmtr(FrmtrMsgInterface $frmtrMsg): void
    {
        $this->msgFrmtr = $frmtrMsg;
    }

    /**
     * setLocale
     * @param LocaleInterface $locale
     */
    public function setLocale(LocaleInterface $locale): void
    {
        $this->getMsgFrmtr()->setLocale($locale);
    }

    /**
     * format
     * @param TagVoidInterface<VendorSpecificDefinition> $value
     * @return string
     */
    public function format($value): string
    {
        $z = $value->generateOpeningTag();

        /**
         * if it is a tag (not a void tag) then go ahead and generate the inner html and the closing tag
         */
        if ($value instanceof TagInterface) {
            /** @var TagVoidInterface<VendorSpecificDefinition>|MsgInterface|string $item */
            foreach ($value->getChildren() as $item) {
                $z .= $this->formatInnerHtmlRecurse($item);
            }
            $z .= $value->generateClosingTag();
        }

        return $z;
    }

    /**
     * formatInnerHtmlRecurse
     * @param TagVoidInterface<VendorSpecificDefinition>|MsgInterface|string $value
     * @return string
     */
    protected function formatInnerHtmlRecurse(TagVoidInterface|MsgInterface|string $value): string
    {
        if ($value instanceof TagVoidInterface) {
            return $this->format($value);
        } elseif ($value instanceof MsgInterface) {
            return htmlspecialchars($this->getMsgFrmtr()->format($value), ENT_HTML5 | ENT_COMPAT);
        } /**
         * is a literal string, not to be translated
         */
        else {
            return $value;
        }
    }
}
