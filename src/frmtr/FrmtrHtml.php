<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\frmtr;

use pvc\interfaces\frmtr\html\FrmtrHtmlInterface;
use pvc\interfaces\frmtr\msg\FrmtrMsgInterface;
use pvc\interfaces\html\tag\TagInterface;
use pvc\interfaces\html\tag\TagVoidInterface;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\intl\Locale;

/**
 * Class FrmtrHtml
 */
class FrmtrHtml implements FrmtrHtmlInterface
{
    protected FrmtrMsgInterface $msgFrmtr;

    public function __construct(FrmtrMsgInterface $msgFrmtr)
    {
        $this->setMsgFrmtr($msgFrmtr);
        $locale = new Locale();
        $locale->setLocaleString(locale_get_default());
        $this->setLocale($locale);
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
     * @param FrmtrMsgInterface $msgFrmtr
     */
    public function setMsgFrmtr(FrmtrMsgInterface $msgFrmtr): void
    {
        $this->msgFrmtr = $msgFrmtr;
    }

    /**
     * format
     * @param TagVoidInterface|TagInterface $value
     * @return string
     */
    public function format($value): string
    {
        $z = $value->generateOpeningTag();

        if ($value instanceof TagInterface) {
            foreach ($value->getInnerHtml() as $item) {
                $z .= $this->formatInnerHtmlRecurse($item);
            }
            $z .= $value->generateClosingTag();
            return $z;
        }

        return $z;
    }

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