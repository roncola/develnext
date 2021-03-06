<?php
namespace ide\formats;

use ide\editors\AbstractEditor;
use ide\editors\ProjectEditor;
use ide\editors\WelcomeEditor;
use ide\project\control\AbstractProjectControlPane;
use php\lib\fs;
use php\lib\reflect;

/**
 * @package ide\formats
 */
class ProjectFormat extends AbstractFormat
{

    /**
     * @var AbstractProjectControlPane[]
     */
    protected $controlPanes = [];

    /**
     * @param AbstractProjectControlPane $pane
     */
    public function addControlPane(AbstractProjectControlPane $pane)
    {
        $this->controlPanes[reflect::typeOf($pane)] = $pane;
    }

    public function addControlPanes(array $panes)
    {
        foreach ($panes as $pane) $this->addControlPane($pane);
    }

    /**
     * @param $file
     *
     * @param array $options
     * @return AbstractEditor
     */
    public function createEditor($file, array $options = [])
    {
        return new ProjectEditor($file, $this->controlPanes);
    }

    public function getIcon()
    {
        return 'icons/myProject16.png';
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function isValid($file)
    {
        return fs::ext($file) == 'dnproject';
    }

    /**
     * @param $any
     *
     * @return mixed
     */
    public function register($any)
    {

    }
}