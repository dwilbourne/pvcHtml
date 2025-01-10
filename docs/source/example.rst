=======
Example
=======

<?php

declare (strict_types=1);

namespace pvcExamples\html;

use League\Container\Container;
use PHPUnit\Framework\TestCase;
use pvc\frmtr\html\FrmtrHtml;
use pvc\html\htmlBuilder\definitions\AbstractDefinitionFactory;
use pvc\html\htmlBuilder\definitions\implementations\league\LeagueContainer;
use pvc\html\htmlBuilder\definitions\implementations\league\LeagueDefinitionFactory;
use pvc\html\htmlBuilder\HtmlFactory;
use pvc\intl\Locale;
use pvc\msg\MsgFrmtr;

class FileUploadFormTest extends TestCase
{
    public function testForm(): void
    {
        $nativeContainer = new League\Container\Container();
        $container = new LeagueContainer($nativeContainer);

        $leagueDefinitionsFactory = new LeagueDefinitionFactory();
        $abstractDefinitionsFactory = new AbstractDefinitionFactory($leagueDefinitionsFactory);

        $htmlBuilder = new HtmlFactory($container, $abstractDefinitionsFactory);

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
        $form = $htmlBuilder->makeElement('form')
                        ->setAttribute('method', 'post')
                        ->setAttribute('action', 'file://target.php');

        /**
         * short form using magic 'setter'
         */
        $form = $htmlBuilder->makeElement('form')->method('post')->action('file://target.php');

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

The convention for naming such a message catalog file is <domain>.<locale>.<filetype>.  Let's say that the domain for
these messages is something spectacularly uncreative, such as 'messages'.  Thus, the filename for this message catalog
would be 'messages.en.php'.

The next step is to instantiate a DomainCatalog object.  That object requires a loaderFactory, which is responsible for the
mechanics of retrieving the messages from the repository and stuffing them into the DomainCatalog object via its
'load' method.  Then, we can create a MsgFrmtr object, which is created with the DomainCatalog object as its
argument, like so::

            $messagesDirectory = 'path/to/some/messagesFiles/';
            $loaderFactory = new DomainCatalogFileLoaderPHP($messagesDirectory);
            $domainCatalog = new DomainCatalog($loaderFactory);
            $domainCatalog->load($domain, $locale);
            $frmtr = new MsgFrmtr($domainCatalog);

If the steps seem a bit painful, it is due to the two layers of abstraction embedded in the process.  The
first is that messages might not kept in files - they could, for example, be kept in a database. The
second is that even if the messages are in files, we potentially need flexibility to handle different file
formats.  For example, yaml, XLIFF, and json are other possible file types.

Construction of a message requires three parameters:

1. the message id - i.e. the key in the array above which is returned by the message catalog
2. an array of parameters used in the message
3. a 'message domain', representing a group of messages

In this case, the code would look like this::

            $domain = 'messages';
            $testMsgId = 'invitation_title';
            $parameters = ['organizer_gender' => 'female', 'organizer_name' => 'Jane'];
            $value = new Msg($testMsgId, $parameters, $domain);

The way to produce the formatted output is by calling the 'format' method on the formatter with the Msg object as its
parameter.  Like so::

            $frmtr->format($value);

This produces 'Jane has invited you to her party!'.

The pvc mechanics here are straightforward, and the more complicated part is understanding how to create
messages with parameters of different types like dates, numbers, money, etc.  Examples of those things can be found
in a companion pvc library called php_lang_tests.