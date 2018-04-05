<?php

namespace TodoListBundle\Controller;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use TodoListBundle\Entity\Task;
use TodoListBundle\Entity\TaskList;
use TodoListBundle\Form\Type\AddListType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TodoListBundle\Form\Type\AddTaskType;

class TaskListController extends Controller
{
    /**
     * @Route("/")
     * @Template()
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

    /**
     * @Route("/edit", name="edit_list")
     * @Template()
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $listName = $request->query->get('listName');
        $listId = $request->query->get('listID');
        $list = $em->getRepository('TodoListBundle:TaskList')->findOneBy(array('id'=>$listId));
        $form = $this->createForm(AddTaskType::class);
        $form->handleRequest($request);

        //if form is submitted
        if($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->get('add_task');
            if(count($formData) > 0){
                $task = new Task();
                $task->setTitle($formData['newTaskName']);;
                $dueDate = new \DateTime($formData['newTaskDueDate']['year']."-".$formData['newTaskDueDate']['month']."-".$formData['newTaskDueDate']['day']);
                $task->setDueDate($dueDate);
                $task->setDescription($formData['newTaskDesc']);
                $task->setListId($list);

                $em->persist($task);
                $em->flush();
                $this->addFlash('success',"The new task has been saved successfully");
            }

        }
        $tasks = $em->getRepository('TodoListBundle:Task')->findBy(array('listId'=>$listId,'status'=>"Pending"));
        $completedTasks = $em->getRepository('TodoListBundle:Task')->findBy(array('listId'=>$listId,'status'=>"completed"));

        return $this->render('TodoListBundle:TaskList:edit.html.twig',array(
            'listName' => $listName,
            'form' => $form->createView(),
            'tasks' => $tasks,
            'completedTasks' => $completedTasks

        ));
    }
}
