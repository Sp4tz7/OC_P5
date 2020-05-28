<?php

namespace FormBuilder;

use Core\Form\FormBuilder;
use Core\Form\MaxLengthValidator;
use Core\Form\NotNullValidator;
use Core\Form\StringField;
use Core\Form\TextField;

class UserFormBuilder extends FormBuilder
{
    public function build()
    {
        $this->form->add(
            new StringField(
                [
                    'label'      => 'Firstname',
                    'name'       => 'firstname',
                    'maxLength'  => 20,
                    'validators' => [
                        new MaxLengthValidator('L\'auteur spécifié est trop long (20 caractères maximum)', 20),
                        new NotNullValidator('Merci de spécifier l\'auteur de la news'),
                    ],
                ]
            )
        )->add(
            new StringField(
                [
                    'label'      => 'Lastname',
                    'name'       => 'lastname',
                    'maxLength'  => 100,
                    'validators' => [
                        new MaxLengthValidator(
                            'Le titre spécifié est trop long (100 caractères maximum)',
                            100
                        ),
                        new NotNullValidator('Merci de spécifier le titre de la news'),
                    ],
                ]
            )
        )->add(
            new StringField(
                [
                    'label'      => 'Email',
                    'name'       => 'email',
                    'maxLength'  => 100,
                    'validators' => [
                        new NotNullValidator('Merci de spécifier le contenu de la news'),
                    ],
                ]
            )
        )->add(
            new StringField(
                [
                    'label'      => 'Password',
                    'name'       => 'password',
                    'maxLength'  => 100,
                    'validators' => [
                        new NotNullValidator('Merci de spécifier le contenu de la news'),
                    ],
                ]
            )
        )->add(
            new StringField(
                [
                    'label'      => 'Nickname',
                    'name'       => 'nickname',
                    'maxLength'  => 100,
                    'validators' => [
                        new NotNullValidator('Merci de spécifier le contenu de la news'),
                    ],
                ]
            )
        );
    }
}