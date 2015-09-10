<?php
namespace ide\editors\value;

use ide\editors\FormEditor;
use ide\systems\FileSystem;
use php\gui\designer\UXDesignPropertyEditor;
use php\gui\framework\DataUtils;
use php\gui\framework\Timer;
use php\gui\UXApplication;
use php\gui\UXNode;
use php\gui\UXTableCell;
use php\lang\IllegalArgumentException;
use php\lang\JavaException;
use php\xml\DomElement;

/**
 * Class ElementPropertyEditor
 * @package ide\editors\value
 */
abstract class ElementPropertyEditor extends UXDesignPropertyEditor
{
    /**
     * @var UXNode
     */
    protected $content;

    /**
     * @var string
     */
    protected $tooltip;

    /**
     * @var callable
     */
    protected $getter;

    /**
     * @var callable
     */
    protected $setter;

    /**
     * @var ElementPropertyEditor[]
     */
    protected static $editors = [];

    /**
     * ElementPropertyEditor constructor.
     *
     * @param callable $getter
     * @param callable $setter
     */
    public function __construct(callable $getter = null, callable $setter = null)
    {
        $this->getter = $getter;
        $this->setter = $setter;

        $this->content = $this->makeUi();
    }

    /**
     * @return string
     */
    abstract public function getCode();

    /**
     * @return UXNode
     */
    abstract public function makeUi();

    /**
     * @param $value
     */
    public function updateUi($value)
    {
        Timer::run(100, function () {
            $editor = FileSystem::getSelectedEditor();

            if ($editor instanceof FormEditor) {
                $editor->getDesigner()->update();
            }
    });
    }

    /**
     * @param DomElement $element
     *
     * @return ElementPropertyEditor
     */
    abstract public function unserialize(DomElement $element);

    /**
     * @param string $tooltip
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = "$tooltip";
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getNormalizedValue($value)
    {
        return $value;
    }

    public function getCssNormalizedValue($value)
    {
        return $value;
    }

    /**
     * @param UXTableCell $cell
     * @param bool $empty
     *
     * @return mixed
     */
    public function update(UXTableCell $cell, $empty)
    {
        $cell->graphic = $this->content;
        $this->updateUi($this->getNormalizedValue($this->getValue()));
    }

    public function setAsFormConfigProperty($defaultValue)
    {
        $this->setter = function (ElementPropertyEditor $editor, $value) {
            $target = $this->designProperties->target;

            if ($target->userData instanceof FormEditor) {
                $target->userData->getConfig()->set($editor->code, $value);
            }
        };

        $this->getter = function (ElementPropertyEditor $editor) use ($defaultValue) {
            $target = $this->designProperties->target;

            if ($target->userData instanceof FormEditor) {
                return $target->userData->getConfig()->get($editor->code, $defaultValue);
            }

            return '';
        };
    }

    /**
     * @return $this
     */
    public function setAsDataProperty()
    {
        $this->setter = function (ElementPropertyEditor $editor, $value) {
            $target = $this->designProperties->target;

            if ($target->id) {
                $data = DataUtils::get($target);
                $data->set($editor->code, $value);
            }
        };

        $this->getter = function (ElementPropertyEditor $editor) {
            $target = $this->designProperties->target;

            if ($target->id) {
                $data = DataUtils::get($target);

                return $data->get($editor->code);
            }

            return '';
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function setAsCssProperty()
    {
        $this->setter = function (ElementPropertyEditor $editor, $value) {
            $target = $this->designProperties->target;
            $target->css($editor->code, $editor->getCssNormalizedValue($value));
        };

        $this->getter = function (ElementPropertyEditor $editor) {
            $target = $this->designProperties->target;
            return $target->css($editor->code);
        };

        return $this;
    }

    public function applyValue($value, $updateUi = true)
    {
        $value = $this->getNormalizedValue($value);

        try {
            if (!$this->setter) {
                $this->designProperties->target->{$this->code} = $value;
            } else {
                $setter = $this->setter;
                $setter($this, $value);
            }

            if ($updateUi) {
                $this->updateUi($value);
            }
        } catch (IllegalArgumentException $e) {
            ;
        } catch (JavaException $e) {
            if (!$e->isIllegalArgumentException()) {
                throw $e;
            }
        }
    }

    public function getValue()
    {
        try {
            if (!$this->getter) {
                $value = $this->designProperties->target->{$this->code};
                return $value;
            } else {
                $getter = $this->getter;

                return $getter($this);

            }
        } catch (IllegalArgumentException $e) {
            ;
        } catch (JavaException $e) {
            if (!$e->isIllegalArgumentException()) {
                throw $e;
            }
        }
    }

    /**
     * @param ElementPropertyEditor $editor
     */
    public static function register(ElementPropertyEditor $editor)
    {
        static::$editors[$editor->getCode()] = $editor;
    }

    /**
     * @param $code
     *
     * @return ElementPropertyEditor
     * @throws \Exception
     */
    public static function getByCode($code)
    {
        $editor = static::$editors[$code];

        if (!$editor) {
            throw new \Exception("Unable to find the '$code' editor");
        }

        return $editor;
    }
}