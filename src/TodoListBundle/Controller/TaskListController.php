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
    public function indexAction(Request $request) //homepage action
    {
        $em = $this->getDoctrine()->getManager(); // initialise doctrine
        $form = $this->createForm(AddListType::class); //form creation using custom type AddListType
        $form->handleRequest($request);

        //if form is submitted
        if($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->get('add_list'); //we get submited data
            if(count($formData) > 0){ // if there is data
                $list = new TaskList(); //init of new TaskList object
                $list->setName($formData['newListName']);
                $date = new \DateTime('NOW');
                $list->setCreated($date);
                $dueDate = new \DateTime($formData['newListDueDate']['year']."-".$formData['newListDueDate']['month']."-".$formData['newListDueDate']['day']);
                $list->setDueDate($dueDate);
                $em->persist($list); //mapping into db
                $em->flush();
                // flash message to confirm
                $this->addFlash('success',"The new task list has been saved successfully");
            }

        }

        /*Fetching all taskList in progress*/
        $taskList = $em->getRepository('TodoListBundle:TaskList')->findBy(array('completed'=>false));
        /*Fetching of the Task lists marked as finished*/
        $completedList = $em->getRepository('TodoListBundle:TaskList')->findBy(array('completed'=>true));

        //rendering of the view with params used in it
        return $this->render('TodoListBundle:TaskList:index.html.twig',array(

            'form' => $form->createView(),
            'lists' => $taskList,
            'completedList' => $completedList
        ));
    }

    /**
     * @Route("/edit", name="edit_list")
     * @Template()
     */
    public function editAction(Request $request) //refers to page with all task for a list
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
            'listId' => $listId,
            'form' => $form->createView(),
            'tasks' => $tasks,
            'completedTasks' => $completedTasks

        ));
    }

    /**
     * @Route("/delete_list", name="delete_list")
     * @Template()
     */
    public function deleteListAction(Request $request) // upon click on mark as completed button
    {
        $em = $this->getDoctrine()->getManager();
        $listId = $request->query->get('listID');
        $list = $em->getRepository('TodoListBundle:TaskList')->findOneBy(array('id'=>$listId));
        $name = $list->getName();
        $list->setCompleted(true); //we actually do not delete the list but just set the completed param as completed
        $em->persist($list);
        $em->flush();
        $this->addFlash('success',"The list ".$name." has been deleted");

        $form = $this->createForm(AddListType::class);
        $taskList = $em->getRepository('TodoListBundle:TaskList')->findBy(array('completed'=>false));
        $completedList = $em->getRepository('TodoListBundle:TaskList')->findBy(array('completed'=>true));

        //render of the homepage
        // a simple redirect would be better
        return $this->render('TodoListBundle:TaskList:index.html.twig',array(

            'form' => $form->createView(),
            'lists' => $taskList,
            'completedList' => $completedList

        ));
    }
}
