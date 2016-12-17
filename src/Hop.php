<?php

namespace Qnez\TraceRoute;

class Hop
{
    /**
     * The sequence number for the hop.s.
     *
     * @var int
     */
    private $_sequence = 0;

    private $_step = '';

    private $_duration = null;

    public function __construct($sequence, $step, $duration = null)
    {
        $this->_sequence = $sequence;
        $this->_step = $step;
        $this->_duration = $duration;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string String representation of the object
     */
    public function __toString()
    {
        $text = sprintf("%s\t%s", $this->_sequence, $this->_step);

        if (!empty($this->_duration)) {
            $text .= "\t".number_format($this->_duration / 1000000);
        }

        return $text;
    }

    /**
     * Gets the The sequence number for the hop.s.
     *
     * @return int
     */
    public function getSequence()
    {
        return $this->_sequence;
    }

    /**
     * Gets the value of _step.
     *
     * @return mixed
     */
    public function getStep()
    {
        return $this->_step;
    }

    /**
     * Gets the value of _duration.
     *
     * @return mixed
     */
    public function getDuration()
    {
        return $this->_duration;
    }
}
