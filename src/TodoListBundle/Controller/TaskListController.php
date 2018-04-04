<?php

namespace TodoListBundle\Controller;

use DateTime;
use TodoListBundle\Entity\TaskList;
use TodoListBundle\Form\Type\AddListType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskListController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(AddListType::class);
        $form->handleRequest($request);

        //if form is submitted
        if($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->get('add_list');
            if(count($formData) > 0){
                $list = new TaskList();
                $list->setName($formData['newListName']);
                $date = new \DateTime('NOW');
                $list->setCreated($date);
                $dueDate = new \DateTime($formData['newListDueDate']['year']."-".$formData['newListDueDate']['month']."-".$formData['newListDueDate']['day']);
                $list->setDueDate($dueDate);
                $em->persist($list);
                $em->flush();
                $this->addFlash('success',"The new task list has been saved successfully");
            }

        }

        /*Fetching of the Task lists*/
        $taskList = $em->getRepository('TodoListBundle:TaskList')->findAll();

        return $this->render('TodoListBundle:TaskList:index.html.twig',array(

            'form' => $form->createView(),
            'lists' => $taskList
        ));
    }
}
