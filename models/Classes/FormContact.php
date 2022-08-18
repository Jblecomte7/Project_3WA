<?php

class FormContact extends Form
{
    protected $className;
    protected $type;
    protected $data;

    public function __construct(array $data = [], array $type = [], array $className = [])
    {
        $this->data = $data;
        $this->className = $className;
        $this->type = $type;
    }

    public function label($name): string
    {
        $classe = $this->className['labelClass'];
        return ('<label for="' . $name . '" class="' . $classe . '">' . $this->data[$name] . '</label>');
    }

    public function input($name, $type): string
    {
        $classe = $this->className['inputClass'];
        return ($this->label($name) . '<input class="' . $classe . '" type="' . $type . '" id="' . $name . '" name="' . $name . '" value="' . $this->getValue($name) . '" required maxlength="100"> <br>');
    }

    public function textarea($name): string
    {
        $classe = $this->className['textAreaClass'];
        return ($this->label($name) . '<textarea class="' . $classe . '" id="' . $name . '" name="' . $name . '" required maxlength="300"></textarea> <br>');
    }

    public function submit($text = 'Valider'): string
    {
        $classe = $this->className['submitClass'];
        return ('<input class="' . $classe . '" type="submit" value="' . $text . '">');
    }
}
