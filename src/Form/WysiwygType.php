<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class WysiwygType extends AbstractType
{
    public function getParent() : string
    {
        //Ce champ doit hériter du champ textarea
        return TextareaType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        //ajout automatique de la class wysiwig
        $view->vars['attr']['class'] = ($view->vars['attr']['class'] ?? '') . ' wysiwyg';
    }

}
