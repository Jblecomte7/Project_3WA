<?php

class Form
{
    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    protected function getValue($index)
    {
        if (isset($_POST[$index])) {
            return $_POST[$index];
        }
    }

    protected function label($name): string
    {
        return ('<label for="' . $name . '">' . $this->data[$name] . '</label>');
    }

    public function input($name, $type): string
    {
        return ($this->label($name) . '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" value="' . $this->getValue($name) . '"> <br>');
    }

    public function textarea($name): string
    {
        return ($this->label($name) . '<textarea id="' . $name . '" name="' . $name . '" value="' . $this->getValue($name) . '"></textarea> <br>');
    }

    public function submit($text = 'Envoyer'): string
    {
        return ('<input type="submit" value="' . $text . '">');
    }
}
