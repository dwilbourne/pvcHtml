<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);


use PHPUnit\Framework\TestCase;
use pvc\frmtr\html\FrmtrHtml;
use pvc\html\factory\ContainerFactory;
use pvc\html\factory\HtmlFactory;
use pvc\intl\Locale;
use pvc\msg\MsgFrmtr;

class FileUploadFormTest extends TestCase
{
    public function testForm(): void
    {
        $containerFactory = new ContainerFactory();
        $factory = new HtmlFactory($containerFactory);
        $locale = new Locale();
        $locale->setLocaleString('en');
        /**
         * don't have any messages to translate in this example
         */
        $msgFrmtr = $this->createMock(MsgFrmtr::class);
        $htmlFrmtr = new FrmtrHtml($msgFrmtr, $locale);

        $form = $factory->makeElement('form')->method('post')->action('file://target.php');
        $form->addSubTag('input')->inputtype('file')->name('filename');
        $form->addSubTag('button')->buttontype('submit')->name('btnOK')->value('ok')->addText('Ok');
        $form->addSubTag('button')->buttontype('submit')->name('btnCancel')->value('cancel')->addText('Cancel');

        $expectedOutput = "";
        $expectedOutput .= "<form method='post' action='file://target.php'>";
        $expectedOutput .= "<input type='file' name='filename'>";
        $expectedOutput .= "<button type='submit' name='btnOK' value='ok'>Ok</button>";
        $expectedOutput .= "<button type='submit' name='btnCancel' value='cancel'>Cancel</button>";
        $expectedOutput .= "</form>";

        self::assertEquals($expectedOutput, $htmlFrmtr->format($form));
    }
}
