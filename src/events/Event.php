<?php

namespace sonrac\lumenRest\events;

use Illuminate\Queue\SerializesModels;

/**
 * Class Event
 * Base events class
 *
 * @package sonrac\lumenRest\events
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
abstract class Event
{
    use SerializesModels;
}
