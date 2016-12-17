<?php

namespace Qnez\TraceRoute\Helpers;

/**
 * A simple helper which will calculate the Checksum.
 */
class Checksum
{
    /**
     * Calculates the checksum for the provided data.
     *
     * @return string
     */
    public static function calculate($data)
    {
        $bit = unpack('n*', $data);
        $sum = array_sum($bit);

        if (strlen($data) % 2) {
            $temp = unpack('C*', $data[strlen($data) - 1]);
            $sum += $temp[1];
        }

        $sum = ($sum >> 16) + ($sum & 0xffff);
        $sum += ($sum >> 16);
        var_dump(pack('n*', ~$sum));

        return pack('n*', ~$sum);
    }
}
