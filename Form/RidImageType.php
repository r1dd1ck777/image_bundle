<?php
namespace Rid\Bundle\ImageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RidImageType extends AbstractType
{
    protected $config;
    protected $subscriber;
    /** @var \Symfony\Component\Translation\Translator */
    protected $translator;

    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder->addEventSubscriber($this->subscriber);

        $builder
            ->add('file', 'file', array('required'  => false, 'label' => $this->translator->trans('rid_image.form.file', array(), 'RidImageBundle')))
//        ->add('value', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rid\Bundle\ImageBundle\Model\RidImage',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rid_image';
    }
}
