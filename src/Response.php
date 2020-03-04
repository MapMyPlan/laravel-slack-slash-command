<?php

namespace MapMyPlan\SlashCommand;

use GuzzleHttp\Client;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Response
{
    /** @var Request */
    protected $request;

    /** @var string */
    protected $text;

    /** @var string */
    protected $responseType;

    /** @var string */
    protected $channel;

    /** @var string */
    protected $icon = '';

    /** @var Collection */
    protected $attachments;

    /** @var Collection  */
    protected $blocks;

    /** @var Client */
    protected $client;

    public static function create(Request $request): self
    {
        $client = app(Client::class);

        return new static($client, $request);
    }

    public function __construct(Client $client, Request $request)
    {
        $this->client = $client;

        $this->request = $request;

        $this->channel = $request->channelName;

        $this->displayResponseToUserWhoTypedCommand();

        $this->attachments = new Collection();

        $this->blocks = new Collection();
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function withText(string $text)
    {
        $this->text = $text;

        return $this;
    }

    public function onChannel(string $channelName)
    {
        $this->channel = $channelName;

        return $this;
    }

    /**
     * @return $this
     */
    public function displayResponseToUserWhoTypedCommand()
    {
        $this->responseType = 'ephemeral';

        return $this;
    }

    /**
     * @param  Attachment  $attachment
     *
     * @return $this
     */
    public function withAttachment(Attachment $attachment)
    {
        $this->attachments->push($attachment);

        return $this;
    }

    public function withBlock(Block $block)
    {
        $this->blocks->push($block);

        return $this;
    }

    /**
     * @param array|Attachment  $attachments
     *
     * @return $this
     */
    public function withAttachments($attachments)
    {
        if (! is_array($attachments)) {
            $attachments = [$attachments];
        }

        foreach ($attachments as $attachment) {
            $this->withAttachment($attachment);
        }

        return $this;
    }

    public function withBlocks($blocks)
    {
        if (! is_array($blocks)) {
            $blocks = [$blocks];
        }

        foreach ($blocks as $block) {
            $this->withBlock($block);
        }

        return $this;
    }

    /**
     * @param string $channelName
     *
     * @return $this
     */
    public function displayResponseToEveryoneOnChannel(string $channelName = '')
    {
        $this->responseType = 'in_channel';

        if ($channelName !== '') {
            $this->onChannel($channelName);
        }

        return $this;
    }

    /**
     * Set the icon (either URL or emoji) we will post as.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function useIcon(string $icon)
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIconType(): string
    {
        if (empty($this->icon)) {
            return '';
        }

        if (Str::startsWith($this->icon, ':') && Str::endsWith($this->icon, ':')) {
            return 'icon_emoji';
        }

        return 'icon_url';
    }

    /**
     * Send the response to Slack.
     */
    public function send()
    {
        $this->client->post($this->request->responseUrl, ['json' => $this->getPayload()]);
    }

    public function getIlluminateResponse(): IlluminateResponse
    {
        return new IlluminateResponse($this->getPayload());
    }

    protected function getPayload(): array
    {
        $payload = [
            'text' => $this->text,
            'channel' => $this->channel,
            'link_names' => true,
            'unfurl_links' => true,
            'unfurl_media' => true,
            'mrkdwn' => true,
            'response_type' => $this->responseType,
            'attachments' => $this->attachments->map(function (Attachment $attachment) {
                return $attachment->toArray();
            })->toArray(),
        ];

        if (! empty($this->icon)) {
            $payload[$this->getIconType()] = $this->icon;
        }

        return $payload;
    }
}
