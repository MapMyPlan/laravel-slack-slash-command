<?php


namespace MapMyPlan\SlashCommand;


class BlockText
{
    protected $type;
    protected $text;

    public static function create($type, $text)
    {
        return new static($type, $text);
    }

    public function __construct(string $type, string $text)
    {
        $this->type = $type;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param  string  $type
     * @return BlockText
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param  string  $text
     * @return BlockText
     */
    public function setText(string $text)
    {
        $this->text = $text;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'text' => $this->text,
        ];
    }


}
