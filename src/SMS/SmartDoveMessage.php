<?php

namespace Meditate\SmartDove\SMS;

class SmartDoveMessage
{
    /**
     * The message subject.
     *
     * @var string
     */
    public $subject = null;

    /**
     * The message content.
     *
     * @var string
     */
    public $content;

    /**
     * The message send time.
     *
     * @var \Carbon\Carbon|string
     */
    public $sendTime;

    /**
     * Create a new message instance.
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the message subject.
     *
     * @param string $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the message subject.
     *
     * @param string $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the message send time.
     *
     * @param \Carbon\Carbon|string $sendTime
     * @return $this
     */
    public function sendTime($sendTime)
    {
        $this->sendTime = $sendTime;

        return $this;
    }

    /**
     * Create a new message instance.
     *
     * @param string $content
     * @return static
     */
    public static function create($content)
    {
        return new static($content);
    }
}
