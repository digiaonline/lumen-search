<?php namespace Nord\Lumen\Search;

class Configuration
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
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }


    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }


    /**
     * @param array $config
     */
    private function configure(array $config)
    {
        if (isset($config['separator'])) {
            $this->separator = $config['separator'];
        }

        if (isset($config['delimiter'])) {
            $this->delimiter = $config['delimiter'];
        }
    }
}
