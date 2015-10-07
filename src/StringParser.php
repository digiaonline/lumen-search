<?php namespace Nord\Lumen\Search;

use Nord\Lumen\Core\Exception\InvalidArgument;

class StringParser
{

    /**
     * @var string
     */
    private $separator = '|';

    /**
     * @var string
     */
    private $delimiter = ':';


    /**
     * Configuration constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->configure($config);
    }


    /**
     * @param $string
     *
     * @return array
     * @throws InvalidArgument
     */
    public function parse($string)
    {
        if (!is_string($string)) {
            throw new InvalidArgument('Cannot parse non-string values.');
        }

        $array = [];

        foreach ($this->splitItems($string) as $item) {
            list($property, $value) = $this->splitPropertyAndValue($item);

            $array[$property] = $value;
        }

        return $array;
    }


    /**
     * @param array $config
     */
    protected function configure(array $config)
    {
        if (isset($config['separator'])) {
            $this->separator = $config['separator'];
        }

        if (isset($config['delimiter'])) {
            $this->delimiter = $config['delimiter'];
        }
    }


    /**
     * @param string $string
     *
     * @return array
     */
    protected function splitItems($string)
    {
        return strpos($string, $this->separator) !== false ? explode($this->separator, $string) : [$string];
    }


    /**
     * @param string $string
     *
     * @return array
     */
    protected function splitPropertyAndValue($string)
    {
        return explode($this->delimiter, $string, 2);
    }
}
