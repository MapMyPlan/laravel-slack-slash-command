<?php

namespace MapMyPlan\SlashCommand;

interface HandlesSlashCommand
{
    public function getRequest(): Request;

    public function respondToSlack(string $text = ''): Response;
}
