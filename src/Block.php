<?php


namespace MapMyPlan\SlashCommand;

class Block
{
    protected $type = 'section';
    protected $block_id;
    /** @var BlockText */
    protected $text;
    /** @var BlockAccessory */
    protected $accessory;

    public static function create($type)
    {
        return new Block($type);
    }

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function setBlockId(string $blockId)
    {
        $this->block_id = $blockId;
        return $this;
    }

    public function setText($text)
    {
        if (is_a($text, BlockText::class)) {
            $this->text = $text;
        } elseif (is_array($text)) {
            if (isset($text['type']) && isset($text['text'])) {
                $this->text = BlockText::create($text['type'], $text['text']);
            } elseif (isset($text[0]) && isset($text[1])) {
                $this->text = BlockText::create($text[0], $text[1]);
            } elseif (isset($text[0])) {
                $this->text = BlockText::create('mrkdwn', $text[0]);
            } else {
                $this->text = BlockText::create('mkrdwn', '');
            }
        } elseif (is_string($text)) {
            $this->text = BlockText::create('mrkdwn', $text);
        } else {
            $this->text = BlockText::create('mkrdwn', '');
        }
        return $this;
    }

    public function setAccessory($accessory)
    {
        if (is_a($accessory, BlockAccessory::class)) {
            $this->accessory = $accessory;
        } else {
            $this->accessory = BlockAccessory::create(
                $accessory['type'],
                $accessory['image_url'],
                isset($accessory['alt_text']) ? $accessory['alt_text'] : ''
            );
        }
        return $this;
    }

    public function toArray()
    {
        $response = [
            'type' => $this->type,
        ];

        if (!empty($this->block_id)) {
            $response['block_id'] = $this->block_id;
        }
        if (!empty($this->text)) {
            $response['text'] = $this->text;
        }
        if (!empty($this->accessory)) {
            $response['accessory'] = $this->accessory;
        }
        return $response;
    }
}
