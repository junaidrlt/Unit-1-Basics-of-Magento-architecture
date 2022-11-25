<?php
declare(strict_types=1);
namespace RLTSquare\Training\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{

    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/rltsquare.log';
}
