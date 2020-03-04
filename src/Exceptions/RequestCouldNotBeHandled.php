<?php

namespace MapMyPlan\SlashCommand\Exceptions;

use MapMyPlan\SlashCommand\Request;

class RequestCouldNotBeHandled extends SlackSlashCommandException
{
    public static function noHandlerFound(Request $request)
    {
        return new static('There is no handler found that can handle request '.print_r($request->all(), true));
    }
}
