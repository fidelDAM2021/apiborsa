<?php

namespace App\Form;

use App\Entity\Oferta;
use App\Entity\Empresa;
use App\Entity\Cicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class OfertaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('data',DateType::class, [
                'label'=>"Data",
                'widget'=>'single_text',
                'input'=>'datetime_immutable'
            ])
            ->add('estat',CheckboxType::class,[
                'label'=>"Activa",'required'=>false
            ])
            ->add('textoferta',TextareaType::class)
            ->add('experiencia',TextType::class)
            ->add('idiomes',TextType::class)
            ->add('altres',TextType::class)
            ->add('urloferta',TextType::class)
            ->add('NIFempresa',EntityType::class,
            ['class'=>Empresa::class,
            'label'=>'Empresa',
            'choice_label'=>'nom'])
            ->add('cicle',EntityType::class,
            ['class'=>Cicle::class,
            'choice_label'=>'nomcicle',
            'label'=>"Cicles",
            'expanded'=>true,
            'multiple'=>true])
            ->add('Guardar',SubmitType::class,array(
                'attr'=>array('class'=>'aceptar')
            ))
            ->add('Cancelar',SubmitType::class, array(
                'label' => 'Cancel·lar',
                'attr' => array(                       
                        'formnovalidate'=>'formnovalidate',
                        'class'=>'cancelar'
                ) 
            ))   
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Oferta::class,
        ]);
    }
}
