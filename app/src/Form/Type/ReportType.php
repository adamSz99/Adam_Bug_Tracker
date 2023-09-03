<?php
/**
 * Report form type.
 */

namespace App\Form\Type;

use App\Entity\Report;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ReportType.
 */
class ReportType extends AbstractType
{
    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'required' => true,
                    'attr' => ['max_length' => 255],
                    'label' => $this->translator->trans('label.title'),
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['max_length' => 2000],
                    'label' => $this->translator->trans('label.description'),
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'required' => true,
                    'choices'  => [
                        \App\Entity\Enum\ReportType::BUG->label() => \App\Entity\Enum\ReportType::BUG->value,
                        \App\Entity\Enum\ReportType::UNKNOWN->label() => \App\Entity\Enum\ReportType::UNKNOWN->value,
                        \App\Entity\Enum\ReportType::IMPROVEMENT->label() => \App\Entity\Enum\ReportType::IMPROVEMENT->value,
                        \App\Entity\Enum\ReportType::FEATURE_REQUEST->label() => \App\Entity\Enum\ReportType::FEATURE_REQUEST->value,
                    ],
                ]
            )
            ->add(
                'resolved',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('label.resolved'),
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'label' => $this->translator->trans('label.category'),
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => $this->translator->trans('label.no_category'),
                    'choice_label' => function ($category): string {
                        return $category->getName();
                    },
                ]
            );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Report::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'report';
    }
}
