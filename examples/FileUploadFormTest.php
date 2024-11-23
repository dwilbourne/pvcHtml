<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcExamples\html;

use League\Container\Container;
use PHPUnit\Framework\TestCase;
use pvc\frmtr\html\FrmtrHtml;
use pvc\html\factory\definitions\AbstractDefinitionFactory;
use pvc\html\factory\definitions\implementations\league\LeagueContainer;
use pvc\html\factory\definitions\implementations\league\LeagueDefinitionFactory;
use pvc\html\factory\HtmlFactory;
use pvc\intl\Locale;
use pvc\msg\MsgFrmtr;

class FileUploadFormTest extends TestCase
{
    public function testForm(): void
    {
        /**
         * this is the stock container that comes from the League
         */
        $nativeContainer = new Container();

        /**
         * this is the container that conforms to HtmlContainerInterface which has the 'add' method that allows the
         * pvc code to add definitions to the container
         */
        $container = new LeagueContainer($nativeContainer);

        /**
         * this factory contains the methods to create vendor-specific definitions which will be added to the container.
         */
        $leagueDefinitionsFactory = new LeagueDefinitionFactory();

        /**
         * a simple wrapper
         */
        $abstractDefinitionsFactory = new AbstractDefinitionFactory($leagueDefinitionsFactory);

        $factory = new HtmlFactory($container, $abstractDefinitionsFactory);

        $locale = new Locale();
        $locale->setLocaleString('en');
        /**
         * don't have any messages to translate in this example
         */
        $msgFrmtr = $this->createMock(MsgFrmtr::class);
        $htmlFrmtr = new FrmtrHtml($msgFrmtr, $locale);

        /**
         * long form using public method names to make attributes
         */
        $form = $factory->makeElement('form')
                        ->setAttribute('method', 'post')
                        ->setAttribute('action', 'file://target.php');

        /**
         * short form using magic 'setter'
         */
        $form = $factory->makeElement('form')->method('post')->action('file://target.php');

        $form->setChild('input')->input_type('file')->name('filename');
        $form->setChild('button')->button_type('submit')->name('btnOK')->value('ok')->setInnerText('Ok');
        $form->setChild('button')->button_type('submit')->name('btnCancel')->value('cancel')->setInnerText('Cancel');

        /**
         * automatically generated unique identifer for each child
         */
        $input = $form->getChild('input0');

        /**
         * long form of getter
         */
        self::assertEquals('filename', $input->getAttribute('name')->getValue());

        /**
         * short form, magic getter
         */
        self::assertEquals('filename', $input->name->value);

        /**
         * innerText (which can be either Msg object or a string) has its own getter.
         */
        $btnCancel = $form->getChild('button1');
        self::assertEquals('Cancel', $btnCancel->getInnerText());

        $expectedOutput = "";
        $expectedOutput .= "<form method='post' action='file://target.php'>";
        $expectedOutput .= "<input type='file' name='filename'>";
        $expectedOutput .= "<button type='submit' name='btnOK' value='ok'>Ok</button>";
        $expectedOutput .= "<button type='submit' name='btnCancel' value='cancel'>Cancel</button>";
        $expectedOutput .= "</form>";

        self::assertEquals($expectedOutput, $htmlFrmtr->format($form));
    }
}
