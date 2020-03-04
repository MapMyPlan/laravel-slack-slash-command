<?php


namespace MapMyPlan\SlashCommand;


class BlockAccessory
{
    protected $type;
    protected $image_url;
    protected $alt_text = '';

    public static function create($type, $image_url, $alt_text = '')
    {
        return new static($type, $image_url, $alt_text);
    }

    public function __construct(string $type, string $image_url, string $alt_text)
    {
        $this->type = $type;
        $this->image_url = $image_url;
        $this->alt_text = $alt_text;
    }

    /**
     * @param  string  $type
     * @return BlockAccessory
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param  string  $image_url
     * @return BlockAccessory
     */
    public function setImageUrl(string $image_url)
    {
        $this->image_url = $image_url;
        return $this;
    }

    /**
     * @param  string  $alt_text
     * @return BlockAccessory
     */
    public function setAltText(string $alt_text)
    {
        $this->alt_text = $alt_text;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'image_url' => $this->image_url,
            'alt_text' => $this->alt_text,
        ];
    }


}
