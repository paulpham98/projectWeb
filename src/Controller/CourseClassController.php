<?php

namespace App\Controller;

use App\Entity\CourseClass;
use App\Entity\Student;
use App\Form\ClassType;
use App\Repository\CourseClassRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CourseClassController extends AbstractController
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry;
    }

    /**
     * @Route("/classes/", name="class_index")
     */
    public function classIndex()
    {
        $class = $this->em->getRepository(CourseClass::class)->findAll();
        return $this->render("course_class/index.html.twig", ['class' => $class]);
    }

    /**
     * @Route("/class/detail/{id}", name="class_detail")
     */
    public function classDetail($id)
    {
        $class = $this->em->getRepository(CourseClass::class)->find($id);
        return $this->render("course_class/detail.html.twig", ['class' => $class]);
    }
    /**
     * @Route("/class/detail/{id}/view", name="class_view")
     */
    public function classView($id)
    {
        $class = $this->em->getRepository(CourseClass::class)->find($id);
        return $this->render("course_class/view.html.twig", ['class' => $class]);
    }
    /**
     * @Route("/class/detail/{id}/student", name="class_student")
     */
    public function classStudent($id)
    {
        $class = $this->em->getRepository(CourseClass::class)->find($id);
        $student = $this->em->getRepository(Student::class)->findAll();
        return $this->render("course_class/view_student.html.twig", ['student' => $student, 'class' => $class]);
    }

    /**
     * @Route("/class/detail/{id}/view/remove", name="class_student_remove" )
     */
    public function removeStudent(Request $request, $id)
    {
        $inputValue = $request->get('checkbox');
        $class = $this->em->getRepository(CourseClass::class)->find($id);
        for ($i = 0; $i < count((array)$inputValue); $i++) {
            $iv = $inputValue[$i];
            $student = $this->em->getRepository(Student::class)->find($iv);
            $student->removeCourseClass($class);
            $class->removeStudent($student);
        }
        $manager = $this->em->getManager();
        $manager->flush();
        $this->addFlash("Success", "Remove student succeed");
        return $this->redirectToRoute("class_view", ['id' => $id]);
    }

    /**
     * @Route("/class/detail/{id}/view/assign", name="class_student_assign" )
     */
    public function assignStudent(Request $request, $id)
    {
        $class = $this->em->getRepository(CourseClass::class)->find($id);
        $max_student = $class->getMaximum();
        $inputValue = $request->get('checkbox');
        $number_assign = count((array)$inputValue);
        $number_student = $request->get('number_students');
        if ($number_student + $number_assign <= $max_student) {
            for ($i = 0; $i < $number_assign; $i++) {
                $iv = $inputValue[$i];
                $student = $this->em->getRepository(Student::class)->find($iv);
                $student->addCourseClass($class);
                $class->addStudent($student);
            }
            $manager = $this->em->getManager();
            $manager->flush();
            $this->addFlash("Success",  "Assign Student Succeed");
        } else {
            $this->addFlash("Error",  "Class is full");
        }
        return $this->redirectToRoute("class_view", ['id' => $id]);
    }

    /**
     * @Route("/class/delete/{id}", name="class_delete" )
     */
    public function deleteClass(Request $request, $id)
    {
        if ($id == 'checkbox') {
            $inputValue = $request->get('checkbox');
            for ($i = 0; $i < count((array)$inputValue); $i++) {
                $iv = $inputValue[$i];
                $class = $this->em->getRepository(CourseClass::class)->find($iv);
                $manager = $this->em->getManager();
                $manager->remove($class);
            }
            $manager->flush();
            $this->addFlash("Success", "Delete class succeed");
        } else {
            $class = $this->em->getRepository(CourseClass::class)->find($id);
            if ($class == null) {
                $this->addFlash("Error", "Delete class failed");
            } else {
                $manager = $this->em->getManager();
                $manager->remove($class);
                $manager->flush();
                $this->addFlash("Success", "Delete class succeed");
            }
        }
        return $this->redirectToRoute("class_index");
    }

    /**
     * @Route("/class/add/", name="class_add" )
     */
    public function addCourse(Request $request)
    {
        $class = new CourseClass();
        $form = $this->createForm(ClassType::class, $class);
        $form->handleRequest($request);
        $site = 'add';

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->em->getManager();
            $manager->persist($class);
            $manager->flush();
            return $this->redirectToRoute("class_index");
        }

        return $this->renderForm("course_class/add_edit.html.twig", ['form' => $form, 'site' => $site]);
    }

    /**
     * @Route("/class/edit/{id}", name="class_edit" )
     */
    public function editCourse(Request $request, $id)
    {
        $class = $this->em->getRepository(CourseClass::class)->find($id);
        $form = $this->createForm(ClassType::class, $class);
        $form->handleRequest($request);
        $site = 'edit';

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->em->getManager();
            $manager->persist($class);
            $manager->flush();
            return $this->redirectToRoute("class_index");
        }

        return $this->renderForm("course_class/add_edit.html.twig", ['form' => $form, 'site' => $site]);
    }

    /**
     * @Route("/class/search", name="class_search" )
     */
    public function searchCourseFunction(CourseClassRepository $repo, Request $request)
    {
        $sort = 'asc';
        $search = $request->get('searchValue');
        $class = $repo->searchClass($search);
        return $this->render("course_class/index.html.twig", ['class' => $class, 'sort' => $sort]);
    }
}
