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
    protected $stateArr = array();

    /**
     * @var callable Change callback
     */
    protected $onChangeCallback;

    /**
     * @param int $state
     * @param callable $onChangeCallback
     */
    public function __construct(& $state = 0, callable $onChangeCallback = null)
    {
        $this->state =& $state;
        $this->onChangeCallback = $onChangeCallback;

        if (!static::$flags) {
            static::initialize();
        }

        foreach (static::$flags as $name => $flag) {
            $this->stateArr[$name] = $this->has($flag);
        }
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
     * Has Option
     * @param int $flag
     * @return bool
     */
    public function has($flag)
    {
        return ($this->state & 1 << $flag) > 0;
    }

    /**
     * Get class options
     * @return array
     */
    public static function getFlags()
    {
        if (!static::$flags) {
            static::initialize();
        }

        return self::$flags;
    }

    /**
     * Has Not Option
     *
     * @param int $flag
     * @return bool
     */
    public function hasNot($flag)
    {
        return !$this->has($flag);
    }

    /**
     * Get State
     *
     * @return array
     */
    public function state()
    {
        return $this->stateArr;
    }

    /**
     * Add an option
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
        if ($this->onChangeCallback) {
            $cb = $this->onChangeCallback;
            call_user_func_array($cb, array($this));
        }
    }

    /**
     * Remove an option
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