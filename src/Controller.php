<?php

namespace MapMyPlan\SlashCommand;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Routing\Controller as IlluminateController;
use MapMyPlan\SlashCommand\Exceptions\InvalidHandler;
use MapMyPlan\SlashCommand\Exceptions\InvalidRequest;
use MapMyPlan\SlashCommand\Exceptions\RequestCouldNotBeHandled;
use MapMyPlan\SlashCommand\Exceptions\SlackSlashCommandException;
use MapMyPlan\SlashCommand\Handlers\SignatureHandler;

class Controller extends IlluminateController
{
    /** @var \MapMyPlan\SlashCommand\Request */
    protected $request;

    /** @var \Illuminate\Support\Collection */
    protected $config;

    public function __construct(IlluminateRequest $request, Repository $config)
    {
        $this->request = Request::createFromIlluminateRequest($request);

        $this->config = collect($config->get('laravel-slack-slash-command'));
    }

    public function getResponse(IlluminateRequest $request): IlluminateResponse
    {
        $this->guardAgainstInvalidRequest($request);

        $handler = $this->determineHandler();

        try {
            if ($handler instanceof SignatureHandler) {
                $handler->validate();
            }
            $response = $handler->handle($this->request);
        } catch (SlackSlashCommandException $exception) {
            $response = $exception->getResponse($this->request);
        } catch (Exception $exception) {
            $response = $this->convertToResponse($exception);
        }

        return $response->getIlluminateResponse();
    }

    protected function guardAgainstInvalidRequest(IlluminateRequest $request)
    {
        if ($this->config->get('verify_with_signing')) {
            $this->verifyWithSigning($request);
        } else {
            $this->verifyWithToken($request);
        }
    }

    protected function verifyWithSigning(IlluminateRequest $request)
    {
        $signature = app(RequestSignature::class)->create($request);

        if ($request->header('X-Slack-Signature') !== $signature) {
            throw InvalidRequest::invalidSignature($signature);
        }
    }

    protected function verifyWithToken(IlluminateRequest $request)
    {
        if (! $request->has('token')) {
            throw InvalidRequest::tokenNotFound();
        }

        $validTokens = $this->config->get('token');

        if (! is_array($validTokens)) {
            $validTokens = [$validTokens];
        }

        if (! in_array($this->request->get('token'), $validTokens)) {
            throw InvalidRequest::invalidToken($this->request->get('token'));
        }
    }

    /**
     * @return \MapMyPlan\SlashCommand\Handlers\BaseHandler
     *
     * @throws \MapMyPlan\SlashCommand\Exceptions\RequestCouldNotBeHandled
     */
    protected function determineHandler()
    {
        $handler = collect($this->config->get('handlers'))
            ->map(function (string $handlerClassName) {
                if (! class_exists($handlerClassName)) {
                    throw InvalidHandler::handlerDoesNotExist($handlerClassName);
                }

                return new $handlerClassName($this->request);
            })
            ->filter(function (HandlesSlashCommand $handler) {
                return $handler->canHandle($this->request);
            })
            ->first();

        if (! $handler) {
            throw RequestCouldNotBeHandled::noHandlerFound($this->request);
        }

        return $handler;
    }

    protected function convertToResponse(Exception $exception) : Response
    {
        $message = config('app.debug') ? (string) $exception : 'Whoops, something went wrong...';

        $exception = new SlackSlashCommandException(
            $message,
            $exception->getCode(),
            $exception
        );

        $response = $exception->getResponse($this->request);

        return $response;
    }
}
