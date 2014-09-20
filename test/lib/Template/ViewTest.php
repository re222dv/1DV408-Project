<?php

namespace Template\test;

use Di\Injector;
use Template\View;
use Template\ViewSettings;

require_once('src/lib/Template/template.php');

global $renderedPage;
$renderedPage = <<<PAGE
<!DOCTYPE html>
<html>
    <head>
        <title>Test</title>
    </head>
    <body>\n        \n
    Hello, World!


    </body>
</html>

PAGE;

global $renderedPageXSS;
$renderedPageXSS = <<<PAGE
<!DOCTYPE html>
<html>
    <head>
        <title>Test</title>
    </head>
    <body>
        \n    Hello, &lt;script type=&quot;application/javascript&quot;&gt;alert('hej')&lt;/script&gt;!



    </body>
</html>

PAGE;

class ViewTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var ViewSettings
     */
    private $viewSettings;

    public function __construct() {
        $injector = new Injector();
        $injector->bindToInstance(Injector::class, $injector);

        $this->viewSettings = $injector->get(ViewSettings::class);
        $this->viewSettings->templatePath = __DIR__.'/templates/';
    }

    public function testRender() {
        global $renderedPage;
        $page = new Layout($this->viewSettings, new Content($this->viewSettings));
        $this->assertEquals($renderedPage, $page->render());
    }

    public function testInjectedView() {
        global $renderedPage;
        $page = new LayoutWithInjectedContent($this->viewSettings);
        $page->setVariable('title', 'Test');
        $this->assertEquals($renderedPage, $page->render());
    }

    public function testForWithIf() {
        $page = new UsersView($this->viewSettings);
        $this->assertEquals("\n   Test \n\n  \n\n   Test2 \n\n", $page->render());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Template file '' don't exists
     */
    public function testThrowWhenTemplateNotSet() {
        $page = new NoTemplate($this->viewSettings);
        $page->render();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Template file 'not-found.html' don't exists
     */
    public function testThrowWhenTemplateNotFound() {
        $page = new NotFoundTemplate($this->viewSettings);
        $page->render();
    }

    public function testVariableInsertionIsXssProof() {
        global $renderedPageXSS;
        $content = new Content($this->viewSettings);
        $content->setVariable('name',
                              '<script type="application/javascript">alert(\'hej\')</script>');
        $content->setVariable('hasName', true);
        $page = new Layout($this->viewSettings, $content);

        $this->assertEquals(serialize($renderedPageXSS), serialize($page->render()));
    }
}

class Layout extends View {
    public function __construct(ViewSettings $settings, Content $content) {
        parent::__construct($settings);

        $this->template = 'layout.html';
        $this->setVariable('title', 'Test');
        $this->setView('content', $content);
    }
}


class Content extends View {
    public function __construct(ViewSettings $settings) {
        parent::__construct($settings);

        $this->template = 'content.html';
        $this->setVariable('hasName', false);
    }
}

class NoTemplate extends View {}
class NotFoundTemplate extends View {
    public function __construct(ViewSettings $settings) {
        parent::__construct($settings);

        $this->template = 'not-found.html';
    }
}

class LayoutWithInjectedContent extends View {
    protected $template = 'layoutWithInjectedContent.html';
}

class UsersView extends View {
    protected $template = 'users.html';
    protected $variables = [
        'users' => [
            'Test',
            null,
            'Test2'
        ]
    ];
}
