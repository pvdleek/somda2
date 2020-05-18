<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class BaseForm extends AbstractType
{
    protected const KEY_ATTRIBUTES = 'attr';
    protected const KEY_ATTRIBUTES_ROWS = 'rows';
    protected const KEY_ATTRIBUTES_COLS = 'cols';
    protected const KEY_CHOICE_LABEL = 'choice_label';
    protected const KEY_CHOICE_VALUE = 'choice_value';
    protected const KEY_CHOICES = 'choices';
    protected const KEY_CLASS = 'class';
    protected const KEY_CONSTRAINTS = 'constraints';
    public const KEY_DATA = 'data';
    protected const KEY_EXPANDED = 'expanded';
    protected const KEY_FIRST_OPTIONS = 'first_options';
    protected const KEY_FORMAT = 'format';
    protected const KEY_HTML5 = 'html5';
    protected const KEY_INVALID_MESSAGE = 'invalid_message';
    public const KEY_LABEL = 'label';
    protected const KEY_MAPPED = 'mapped';
    protected const KEY_PREFERRED_CHOICES = 'preferred_choices';
    protected const KEY_QUERY_BUILDER = 'query_builder';
    public const KEY_REQUIRED = 'required';
    protected const KEY_SCALE = 'scale';
    protected const KEY_SECOND_OPTIONS = 'second_options';
    protected const KEY_TYPE = 'type';
    protected const KEY_WIDGET = 'widget';
}
