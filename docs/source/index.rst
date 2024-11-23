
.. toctree::
   :hidden:

   install
   example

===============
pvcHtml Overview
===============

The pvcHtml library is meant to replace what are known as partials in templating languages.  Partials can be
cumbersome to read and modify.  The pvcHtml library tries to simplify the process by creating blocks of html as
objects. From there, the blocks can be modified and extended using php in a programmatic way as opposed to taking a
partial, saving it as a different template and then modifying it to suit your current needs.



Design Points
#############

A simple interface.  It's likely that you will interact with only one object in the library: the HtmlFactory object
. That factory allows you to create any valid html 5 element, attribute or event.  You have the option of using a
fluid interface (which will prevent autocompletion in your ide but which is more concise and easier to read) or you
can choose to add attributes and child elements to your structure using conventional method calls.

Valid Html.  Regardless of how you create your elements and attributes, you will get exceptions if you try to create
or access elements and attributes incorrectly. In other words, the library will make sure your structure conforms to the actual specifications of the html language.

All attributes that accept values have their values checked for validity.

The "inner text" of html elements can be either a string or an object that implements MsgInterface from the
pvcInterfaces library.  If you want to internationalize your content, use the pvcMsg library and set up your translation
files. (Or use your own messages and implement MsgInterface).

Exntensible.  The library depends on a number of other pvc libraries plus one third party package:  a dependency
injection container written by the League (chosen because it is lightweight and framework agnostic).  The overall
design of the this library includes a set of json files which articulate the html 5 specification in a vendor-neutral
format.  The library takes those json definitions and builds a DI container which is the basis of the HtmlFactory
object. Some care was taken to isolate the code that takes the json definitions and creates definitions which are
specific to the League's container.  This in turn means that if you would rather use your own container, the
adaptation is simple. 1) implement a single interface (DefinitionFactoryInterface) which consists of 5 methods
(making definitions for elements, attributes, events and a couple of supporting objects). 2) extend your container to implement HtmlContainerInterface.  This is adding a single method to your container called "add" which allows the pvc library code to add definitions to your container.  And that's it.




