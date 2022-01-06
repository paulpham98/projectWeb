<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\throwException;

class CourseController extends AbstractController
{

    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry;
    }

    /**
     * @Route("/courses/", name="course_index")
     */
    public function courseIndex()
    {
        $sort = 'asc';
        $course = $this->em->getRepository(Course::class)->findAll();
        return $this->render("course/index.html.twig", ['course' => $course, 'sort' => $sort]);
    }

    /**
     * @Route("/course/detail/{id}", name="course_detail")
     */
    public function courseDetail($id)
    {
        $course = $this->em->getRepository(Course::class)->find($id);
        return $this->render("course/detail.html.twig", ['course' => $course]);
    }

    /**
     * @Route("/course/edit/{id}", name="course_edit" )
     */
    public function editCourse(Request $request, $id)
    {
        $course = $this->em->getRepository(Course::class)->find($id);
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        $site = 'edit';

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            if ($file != null) {
                $image = $course->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . "." . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('course_image'),
                        $imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $course->setImage($imageName);
            }
            if ($file == null) {
                $imageName = 'course-avatar.png';
                $course->setImage($imageName);
            }

            $manager = $this->em->getManager();
            $manager->persist($course);
            $manager->flush();
            $this->addFlash("Success", "Edit course succeed !");
            return $this->redirectToRoute("course_index");
        }
        return $this->renderForm("course/add_edit.html.twig", ['form' => $form, 'course' => $course, 'site' => $site]);
    }

    /**
     * @Route("/course/delete/{id}", name="course_delete" )
     */
    public function deleteCourse(Request $request, $id)
    {
        if ($id == 'checkbox') {
            $inputValue = $request->get('checkbox');
            for ($i = 0; $i < count((array)$inputValue); $i++) {
                $iv = $inputValue[$i];
                $course = $this->em->getRepository(Course::class)->find($iv);
                $manager = $this->em->getManager();
                $manager->remove($course);
            }
            $manager->flush();
            $this->addFlash("Success", "Delete course succeed");
        } else {
            $course = $this->em->getRepository(Course::class)->find($id);
            if ($course == null) {
                $this->addFlash("Error", "Delete course failed");
            } else {
                $manager = $this->em->getManager();
                $manager->remove($course);
                $manager->flush();
                $this->addFlash("Success", "Delete course succeed");
            }
        }
        return $this->redirectToRoute("course_index");
    }

    /**
     * @Route("/course/add/", name="course_add" )
     */
    public function addCourse(Request $request)
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        $site = "add";

        if ($form->isSubmitted() && $form->isValid()) {
            //anh
            $file = $form['image']->getData();
            if ($file != null) {
                $image = $course->getImage();
                $ranName = uniqid();
                $imgExt = $image->guessExtension();
                $newName = $ranName . "." . $imgExt;
                try {
                    $image->move(
                        $this->getParameter('course_image'),
                        $newName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $course->setImage($newName);
            }
            if ($file == null) {
                $imageName = 'course-avatar.png';
                $course->setImage($imageName);
            }
            // db
            $manager = $this->em->getManager();
            $manager->persist($course);
            $manager->flush();
            return $this->redirectToRoute("course_index");
        }
        return $this->renderForm("course/add_edit.html.twig", ['form' => $form, 'site' => $site]);
    }

    /**
     * @Route("/course/sort/{val}/asc", name="sort_course_asc" )
     */
    public function sortCourseASC(CourseRepository $repo, $val)
    {
        $sort = 'asc';
        $course = $repo->sortASC($val);
        return $this->render("course/index.html.twig", ['course' => $course, 'sort' => $sort]);
    }
    /**
     * @Route("/course/sort/{val}/desc", name="sort_course_desc" )
     */
    public function sortCourseDESC(CourseRepository $repo, $val)
    {
        $sort = 'desc';
        $course = $repo->sortDESC($val);
        return $this->render("course/index.html.twig", ['course' => $course, 'sort' => $sort]);
    }

    /**
     * @Route("/course/search", name="course_search" )
     */
    public function searchCourseFunction(CourseRepository $repo, Request $request)
    {
        $sort = 'search';
        $search = $request->get('searchValue');
        $course = $repo->searchCourse($search);
        return $this->render("course/index.html.twig", ['course' => $course, 'sort' => $sort]);
    }
}
