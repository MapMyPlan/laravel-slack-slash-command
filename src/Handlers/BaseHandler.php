<?php

namespace MapMyPlan\SlashCommand\Handlers;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use MapMyPlan\SlashCommand\Exceptions\SlackSlashCommandException;
use MapMyPlan\SlashCommand\HandlesSlashCommand;
use MapMyPlan\SlashCommand\Jobs\SlashCommandResponseJob;
use MapMyPlan\SlashCommand\Request;
use MapMyPlan\SlashCommand\Response;

abstract class BaseHandler implements HandlesSlashCommand
{
    use DispatchesJobs;

    /** @var \MapMyPlan\SlashCommand\Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function respondToSlack(string $text = ''): Response
    {
        return Response::create($this->request)->withText($text);
    }

    protected function dispatch(SlashCommandResponseJob $job)
    {
        $job->setRequest($this->request);

        return app(Dispatcher::class)->dispatch($job);
    }

    protected function abort($response)
    {
        throw new SlackSlashCommandException($response);
    }

    public function getRequest(): Request
    {
        return $this->getRequest();
    }

    /**
     * If this function returns true, the handle method will get called.
     *
     * @param \MapMyPlan\SlashCommand\Request $request
     *
     * @return bool
     */
    abstract public function canHandle(Request $request): bool;

    /**
     * Handle the given request. Remember that Slack expects a response
     * within three seconds after the slash command was issued. If
     * there is more time needed, dispatch a job.
     *
     * @param \MapMyPlan\SlashCommand\Request $request
     *
     * @return \MapMyPlan\SlashCommand\Response
     */
    abstract public function handle(Request $request): Response;
}
