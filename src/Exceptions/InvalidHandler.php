<?php

namespace MapMyPlan\SlashCommand\Exceptions;

use MapMyPlan\SlashCommand\Handlers\BaseHandler;

class InvalidHandler extends SlackSlashCommandException
{
    public static function handlerDoesNotExist($handler)
    {
        return new static("There is no class named `{$handler}`.");
    }

    public static function handlerDoesNotExtendFromBaseHandler($handler)
    {
        $baseHandlerClass = BaseHandler::class;

        return new static("The handler `{$handler}` must extend the base handler `{$baseHandlerClass}`.");
    }

    public static function signatureIsRequired($handler)
    {
        return new static("You must set a signature property on the `{$handler}`-class.");
    }
}
