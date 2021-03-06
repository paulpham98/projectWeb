<?php

namespace App\Controller;

use App\Entity\CourseDetail;
use App\Form\CourseDetailType;
use App\Repository\CourseDetailRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\throwException;

class CourseDetailController extends AbstractController
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry;
    }
    /**
     * @Route("/courseDetail/api", methods={"GET"}, name="courseDetail_api")
     */
    public function courseDetailAPI(): JsonResponse
    {
        $courses = $this->em->getRepository(CourseDetail::class)->findAll();
        return $this->json(['courses' => $courses], 200);
    }

    /**
     * @Route("/courseDetail", name="courseDetail_index")
     */

    public function courseDetailIndex()
    {
        $courses = $this->em->getRepository(CourseDetail::class)->findAll();
        return $this->render(
            "course_detail/index.html.twig",
            [
                'courses' => $courses
            ]
        );
    }

    /**
     * @Route("/courseDetail/delete/{id}", name="courseDetail_delete")
     */
    public function courseDetailDelete($id)
    {
        $courses = $this->em->getRepository(CourseDetail::class)->find($id);
        if ($courses == null) {
            $this->addFlash("Error", "Delete course failed");
        } else {
            $manager = $this->em->getManager();
            $manager->remove($courses);
            $manager->flush();
            $this->addFlash("Success", "Delete course succeed");
        }
        return $this->redirectToRoute("courseDetail_index");
    }

    /**
     * @Route("/courseDetail/detail/{id}", name="courseDetail_detail")
     */
    public function categoryDetail($id)
    {
        $courses = $this->em->getRepository(CourseDetail::class)->find($id);
        if ($courses == null) {
            $this->addFlash("Error", "Course Detail not found");
            return $this->redirectToRoute("courseDetail_index");
        }
        return $this->render(
            "course_detail/detail.html.twig",
            [
                "courses" => $courses
            ]
        );
    }

    /**
     * @Route("/courseDetail/add", name="courseDetail_add")
     */
    public function courseAdd(Request $request)
    {
        $courses = new CourseDetail();
        $form = $this->createForm(CourseDetailType::class, $courses);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //code upload v?? x??? l?? t??n ???nh
            //B1: l???y t??n ???nh t??? file upload
            $image = $courses->getPhoto();
            /*B2: ?????t t??n m???i cho file ???nh 
            => ?????m b???o t??n ???nh l?? duy nh???t */
            $imgName = uniqid(); //unique id
            //B3: l???y ??u??i ???nh (image extension)
            $imgExtension = $image->guessExtension();
            //Note: c???n s???a l???i code getter/setter c???a Book Entity
            //B4: n???i t??n m???i v?? ??u??i th??nh t??n file ???nh ho??n thi???n
            $imageName = $imgName . "." . $imgExtension;
            //B5: copy ???nh v??o th?? m???c ch??? ?????nh
            try {
                $image->move(
                    $this->getParameter('course_image'),
                    $imageName
                    /* Note: c???n khai b??o ???????ng d???n th?? m???c ch???a ???nh
                  ??? file config/services.yaml */
                );
            } catch (FileException $e) {
                throwException($e);
            }
            //B6: l??u t??n ???nh v??o DB
            $courses->setPhoto($imageName);

            //add d??? li???u v??o DB
            $manager = $this->em->getManager();
            $manager->persist($courses);
            $manager->flush();

            //hi???n th??? th??ng b??o v?? redirect v??? book index
            $this->addFlash("Success", "Add Course succeed !");
            return $this->redirectToRoute("courseDetail_index");
        }

        return $this->renderForm(
            "course_detail/add.html.twig",
            [
                'form' => $form
            ]
        );
    }
    /**
     * @Route("/courseDetail/edit/{id}", name="courseDetail_edit")
     */
    public function courseEdit(Request $request, $id)
    {
        $courses = $this->em->getRepository(CourseDetail::class)->find($id);
        $form = $this->createForm(CourseDetailType::class, $courses);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['photo']->getData();

            if ($file != null) {

                $image = $courses->getPhoto();

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

                $courses->setPhoto($imageName);
            }


            $manager = $this->em->getManager();
            $manager->persist($courses);
            $manager->flush();
            $this->addFlash("Success", "edit Course succeed !");
            return $this->redirectToRoute("courseDetail_index");
        }

        return $this->renderForm(
            "course_detail/edit.html.twig",
            [
                'form' => $form
            ]
        );
    }

    /**
     * @Route("/courseDetail/search", name="search_course_name")
     */

    public function searchcategoryByName(CourseDetailRepository $repository, Request $request)
    {
        $name = $request->get("name");
        $courses = $repository->searchCourseDetail($name);
        return $this->render(
            "course_detail/index.html.twig",
            [
                'courses' => $courses
            ]
        );
    }
}
