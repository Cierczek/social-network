<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', TextType::class, ([
                    "label"=>" ",
                    "attr"=>([
                        "placeholder"=>"Nombre",
                        "class"=>"form-name form-control"
                    ])
                ]))
                ->add('surname', TextType::class, ([
                    "label"=>" ",
                    "attr"=>([
                        "placeholder"=>"Apellido",
                        "class"=>"form-surname form-control"
                    ])
                ]))
                ->add('nick', TextType::class, ([
                    "label"=>" ",
                    "attr"=>([
                        "placeholder"=>"Nick",
                        "class"=>"form-nick form-control nick-input"
                    ])
                ]))
                ->add('email',EmailType::class, ([
                    "label"=>" ",
                    "attr"=>([
                        "placeholder"=>"Correo Electrónico",
                        "class"=>"form-email form-control"
                    ])
                ]))
                 ->add('bio', TextareaType::class, ([
                    "label"=>" ",
                    "attr"=>([
                        "placeholder"=>"Biografia",
                        "class"=>"form-bio form-control"
                    ])
                ]))
                 ->add('image', FileType::class, ([
                    "label"=>" ",
                     "required"=>false,
                     "data_class"=>null,
                    "attr"=>([
                        "class"=>"form-image form-control"
                    ])
                ]))
                ->add('Guardar', SubmitType::class, ([
                    "attr" => ([
                        "class" => "form-submit btn btn-success"
                        ])
                    ]));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BackendBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'backendbundle_user';
    }


}
