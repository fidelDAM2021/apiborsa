<?php

namespace App\Form;

use App\Entity\Curriculum;
use App\Entity\Alumne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class CurriculumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alumne',EntityType::class,[
            'class'=>Alumne::class,
            'disabled'=>true])
            ->add('experiencia',TextType::class)
            ->add('idiomes',TextType::class)
            ->add('estudis',TextareaType::class)
            ->add('competencies',TextareaType::class)
            ->add('pdf',FileType::class, [
                'label'=>'Currículum en PDF',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new File([
                        'maxSize'=>'8192k',
                        'mimeTypes'=>[
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage'=>'Puja un arxiu PDF vàlid',
                    ])
                ]
            ])
            ->add('Guardar',SubmitType::class, array(
                'attr'=>array('class'=>'aceptar')
            ))
            ->add('Cancelar',SubmitType::class, array(
                'label' => 'Cancel·lar',
                'attr' => array(                       
                        'formnovalidate'=>'formnovalidate',
                        'class'=>'cancelar')
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Curriculum::class,
        ]);
    }
}
