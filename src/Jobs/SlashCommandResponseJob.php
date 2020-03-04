<?php

namespace MapMyPlan\SlashCommand\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use MapMyPlan\SlashCommand\HandlesSlashCommand;
use MapMyPlan\SlashCommand\Request;
use MapMyPlan\SlashCommand\Response;

abstract class SlashCommandResponseJob implements ShouldQueue, HandlesSlashCommand
{
    /** @var \MapMyPlan\SlashCommand\Request */
    public $request;

    public function getResponse(): Response
    {
        return Response::create($this->request);
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function respondToSlack(string $text = ''): Response
    {
        return $this->getResponse()->withText($text);
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
