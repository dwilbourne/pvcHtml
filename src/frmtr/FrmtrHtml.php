<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\frmtr;

use pvc\interfaces\frmtr\html\FrmtrHtmlInterface;
use pvc\interfaces\frmtr\msg\FrmtrMsgInterface;
use pvc\interfaces\html\element\ElementInterface;
use pvc\interfaces\html\element\ElementVoidInterface;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * Class FrmtrHtml
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
     * @return string
     */
    public function format($value): string
    {
        $z = $value->generateOpeningTag();

        /**
         * if it is a element (not a void element) then go ahead and generate the inner html and the closing element
         */
        if ($value instanceof ElementInterface) {
            foreach ($value->getNodes() as $item) {
                $z .= $this->formatInnerHtmlRecurse($item);
            }
            $z .= $value->generateClosingTag();
        }

        return $z;
    }

    /**
     * formatInnerHtmlRecurse
     * @return string
     */
    protected function formatInnerHtmlRecurse(ElementVoidInterface|MsgInterface|string $value): string
    {
        if ($value instanceof ElementVoidInterface) {
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
