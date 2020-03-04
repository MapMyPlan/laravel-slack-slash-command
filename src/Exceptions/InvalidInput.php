<?php

namespace MapMyPlan\SlashCommand\Exceptions;

use Exception;
use MapMyPlan\SlashCommand\Attachment;
use MapMyPlan\SlashCommand\Handlers\SignatureHandler;
use MapMyPlan\SlashCommand\Request;
use MapMyPlan\SlashCommand\Response;

class InvalidInput extends SlackSlashCommandException
{
    protected $handler;

    public function __construct($message, SignatureHandler $handler, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->handler = $handler;
    }

    public function getResponse(Request $request): Response
    {
        return parent::getResponse($request)
            ->withAttachment(
                Attachment::create()
                    ->setText($this->handler->getHelpDescription())
            );
    }
}
