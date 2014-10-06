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
        $rendered = $page->render();
        $this->assertEquals($renderedPage, $rendered);
    }

    public function testInjectedView() {
        global $renderedPage;
        $page = new LayoutWithInjectedContent($this->viewSettings);
        $page->setVariable('title', 'Test');
        $this->assertEquals($renderedPage, $page->render());
    }

    public function testForWithIf() {
        $page = new GroupView($this->viewSettings);
        $group = new GroupModel();
        $group->owner = new UserModel('Admin');
        $group->members = [
            new UserModel('Test'),
            new UserModel('Hidden', false),
            new UserModel('Test2')
        ];

        $page->setVariable('group', $group);
        $this->assertEquals("\n  Admin\n\n\n   Test \n\n  \n\n   Test2 \n\n", $page->render());
    }

    public function testExpressions() {
        $page = new ExpressionView($this->viewSettings);
        $this->assertEquals("12\n5\n15\n5\n13.5\n9\n20\n20\n11\n24\n18\n7\n9\n\n ".
            "numIsMoreThanFour \n\n numPlusNumIsTwenty \n numIsLessThanElevenAndMoreThanNine ".
            "\n\n numIsEven \n\n  \n\n", $page->render());
    }

    public function testScope() {
        $page = new ScopeView($this->viewSettings);
        $render = $page->render();
        $this->assertEquals("10\n\n  3\n\n10\n\n  11\n  \n    \n    \n  \n  12\n  \n  13".
            "\n\n10\n\n11\n", $render);
    }

    public function testArray() {
        $page = new ArrayView($this->viewSettings);
        $render = $page->render();
        $this->assertEquals("one, two\none, two\n", $render);
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
        $this->setVariable('content', $content);
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

class GroupView extends View {
    protected $template = 'group.html';
}

class GroupModel {
    public $owner;
    public $members;
}

class UserModel {
    public $name;
    public $show;

    public function __construct($name, $show = true) {
        $this->name = $name;
        $this->show = $show;
    }
}

class ExpressionView extends View {
    protected $template = 'expressions.html';
    protected $variables = [
        'num' => 10
    ];
}

class ScopeView extends View {
    protected $template = 'scope.html';
    protected $variables = [
        'num' => 10
    ];
}

class ArrayView extends View {
    protected $template = 'arrays.html';
    protected $variables = [
        'array' => ['one', 'two']
    ];
}
