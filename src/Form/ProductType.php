<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Doctrine\ORM\EntityManagerInterface;

class ProductType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', ChoiceType::class, [
                'choices' => [
                    // '0' => 'Seleccione una opción',
                    ($this->em->getRepository(Category::class)->findAllCategoryAsc())
                ],
                'choice_value' => 'name',
                'choice_label' => function(?Category $category) {
                    return $category ? strtoupper($category->getName()) : '';
                },
                'choice_attr' => function(?Category $category) {
                    return $category ? ['class' => 'category_'.strtolower($category->getName())] : [];
                },
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('code', null, [
                'required' => true,
                'attr' => [
                    'pattern' => '[A-Za-z0-9]+',
                    'class' => 'codigo-input'
                ]
            ])
            ->add('name', null, [
                'required' => true
            ])            
            ->add('brand', null, [
                'required' => true
            ])
            ->add('price', NumberType::class, [
                'required' => true,
                'attr' => [
                    'step' => '50.01'
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => ['class' => 'tinymce'],
            ])
            ->add('Enviar', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
