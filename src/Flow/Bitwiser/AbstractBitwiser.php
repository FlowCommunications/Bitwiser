<?php
namespace Flow\Bitwiser;

/**
 * Class AbstractBitwiser
 * @package Flow\Bitwiser
 */
class AbstractBitwiser
{
    /**
     * @var array key-value options
     */
    protected static $flags = array();

    /**
     * @var int bitwise integer value
     */
    protected $state;

    /**
     * @var array key-value state
     */
    protected $namedStateArray = array();

    /**
     * @var array key-value state
     */
    protected $valueStateArray = array();

    /**
     * @var callable Change callback
     */
    protected $onChangeCallback;

    /**
     * @param int $state
     * @param callable $onChangeCallback
     */
    public function __construct(& $state = 0, $onChangeCallback = null)
    {
        $this->state =& $state;
        $this->onChangeCallback = $onChangeCallback;

        if (!static::$flags) {
            static::initialize();
        }

        $this->updateStateArr();
    }

    /**
     * Initialize options array
     */
    protected static function initialize()
    {
        $reflection = new \ReflectionClass(get_called_class());
        $constants = $reflection->getConstants();

        foreach ($constants as $name => $value) {
            static::$flags[$name] = $value;
        }
    }

    /**
     * Update state array
     */
    protected function updateStateArr()
    {
        foreach (static::$flags as $name => $flag) {
            $this->namedStateArray[$name] = $this->has($flag);
        }
        foreach (static::$flags as $name => $flag) {
            $this->valueStateArray[$flag] = $this->has($flag);
        }
    }

    /**
     * Has Option
     * @param int $flag
     * @return bool
     */
    public function has($flag)
    {
        return ($this->state & 1 << $flag) > 0;
    }

    /**
     * Get class flags
     * @return array
     */
    public static function getFlags()
    {
        if (!static::$flags) {
            static::initialize();
        }

        return static::$flags;
    }

    /**
     * ! Has Flag
     *
     * @param int $flag
     * @return bool
     */
    public function hasNot($flag)
    {
        return !$this->has($flag);
    }

    /**
     * Get Named State Array
     *
     * @param bool $named
     * @return array
     */
    public function state($named = true)
    {
        if ($named) {
            return $this->namedStateArray;
        } else {
            return $this->valueStateArray;
        }
    }

    /**
     * Add a flag
     *
     * @param int $flag
     * @return $this
     */
    public function add($flag)
    {
        $this->state = $this->state | 1 << $flag;
        $this->onChange();

        return $this;
    }

    /**
     * Triggers on change
     */
    protected function onChange()
    {
        $this->updateStateArr();

        if ($this->onChangeCallback) {
            $cb = $this->onChangeCallback;
            call_user_func_array($cb, array($this));
        }
    }

    /**
     * Remove a flag
     * @param $flag
     * @return $this
     */
    public function remove($flag)
    {
        $this->state = $this->state ^ 1 << $flag;
        $this->onChange();

        return $this;
    }

    /**
     * Set the onChange callback
     * @param callable $callback
     */
    public function setOnChangeCallback(callable $callback)
    {
        $this->onChangeCallback = $callback;
    }

    /**
     * Get the state integer value
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the state integer value
     * @param $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;
        $this->onChange();

        return $this;
    }

}

