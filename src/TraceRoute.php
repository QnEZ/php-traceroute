<?php

namespace Qnez\TraceRoute;

use Qnez\TraceRoute\Helpers\Time;
use Qnez\TraceRoute\Helpers\Checksum;
use Qnez\TraceRoute\Exceptions\DestinationNotReached;

class TraceRoute
{
    /**
     * The host name that needs a trace route to be done on.
     *
     * @var string
     */
    private $_host = '';

    /**
     * The socket that will be used to step through the necessary hops.
     *
     * @var bool
     */
    private $_socket = false;

    /**
     * The targetted IP Address.
     *
     * @var string
     */
    private $_ipAddress = '';

    public $_steps = [];

    const SOL_IP = 0;
    const IP_TTL = 2;

    public function __construct($host)
    {
        $this->_host = $host;
        $this->_socket = @socket_create(AF_INET, SOCK_RAW, getprotobyname('icmp'));
        $this->_ipAddress = gethostbyname($host);
    }

    public function process()
    {
        $id = rand(0, 0xFFFF);
        $sequence = 1;

        while (true) {
            $this->_steps[] = $this->step($id, $sequence);
        }

        var_dump($this->_steps);
    }

    public function step($id, $sequence)
    {
        socket_set_option($this->_socket, self::SOL_IP, self::IP_TTL, $seq);

        $packet = $this->buildPacket($id, $sequence);

        $start = Time::currentTime();
        $timeout = $start + Time::MICROSECOND;

        // ICMP doesn't have a port so just use 0
        socket_sendto($this->_socket, $packet, strlen($packet), 0, $this->_ipAddress, 0);

        $found = false;

        // Loop until the timeout, or we resolve it
        while ($now < $timeout) {
            $read = array($this->_socket);
            $other = array();

            $selected = socket_select($read, $other, $other, 0, $timeout - $now);

            if ($selected === 0) {
                return new Hop($sequence, '*');
            }

            socket_recvfrom($this->_socket, $data, 65535, 0, $rip, $rport);

            // Unpack the data
            $data = unpack('C*', $data);

            // ICMP
            if ($data[10] != 1) {
                continue;
            }

            // Is this our packet?
            if ($data[21] == 0
                // Echo Reply
                && ($data[25] == ($id & 0xFF))
                && ($data[26] == ($id >> 8))
                && ($data[27] == ($seq & 0xFF))
                && ($data[28] == ($seq >> 8))) {
                return new Hop($sequence, gethostbyaddr($rip), Time::milliseconds($now, $start));
            }

            // Update the "now"
            $now = microtime(true) * Time::MICROSECOND;
        }

        throw new DestinationNotReached();
    }

    private function buildPacket($id, $sequence)
    {
        $packet = '';
        $packet .= chr(8); // Type
        $packet .= chr(0); // Code
        $packet .= chr(0); // Header Checksum
        $packet .= chr(0);
        $packet .= chr($id & 0xFF); // Identifier
        $packet .= chr($id >> 8);
        $packet .= chr($sequence & 0xFF); // Sequence Number
        $packet .= chr($sequence >> 8);

        for ($i = 0; $i < 56; ++$i) { // Add 56 bytes of data
            $packet .= chr(0);
        }

        $checksum = Checksum::calculate($packet);

        $packet[2] = $checksum[0];
        $packet[3] = $checksum[1];

        return $packet;
    }
}
