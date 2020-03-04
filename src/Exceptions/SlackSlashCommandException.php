<?php

namespace MapMyPlan\SlashCommand\Exceptions;

use Exception;
use MapMyPlan\SlashCommand\Attachment;
use MapMyPlan\SlashCommand\Request;
use MapMyPlan\SlashCommand\Response;

class SlackSlashCommandException extends Exception
{
    public function getResponse(Request $request): Response
    {
        return Response::create($request)
            ->withAttachment(Attachment::create()
                ->setColor('danger')
                ->setText($this->getMessage())
                ->setFallback($this->getMessage())
            );
    }
}
