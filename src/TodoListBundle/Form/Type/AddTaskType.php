<?php

namespace TodoListBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddTaskType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('newTaskName', TextType::class, array('label' => 'Title', 'required' => true))

            ->add('newTaskDesc', TextType::class, array('label' => 'Description', 'required' => false))

            ->add('newTaskDueDate', DateType::class, array(
                'label' => 'Due date',
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                )))

            ->add('add', SubmitType::class);
    }

}
