<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\throwException;

class CategoryController extends AbstractController
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry;
    }
    /**
     * @Route("/category/api", methods={"GET"}, name="category_api")
     */
    public function categoryAPI(): JsonResponse
    {
        $categorys = $this->em->getRepository(Category::class)->findAll();
        return $this->json(['categorys' => $categorys], 200);
    }

    /**
     * @Route("/category", name="category_index")
     */

    public function categoryIndex()
    {
        $categorys = $this->em->getRepository(Category::class)->findAll();
        return $this->render(
            "category/index.html.twig",
            [
                'categorys' => $categorys
            ]
        );
    }

    /**
     * @Route("/category/delete/{id}", name="category_delete")
     */
    public function categoryDelete($id)
    {
        $categorys = $this->em->getRepository(Category::class)->find($id);
        if ($categorys == null) {
            $this->addFlash("Error", "Delete category failed");
        } else {
            $manager = $this->em->getManager();
            $manager->remove($categorys);
            $manager->flush();
            $this->addFlash("Success", "Delete category succeed");
        }
        return $this->redirectToRoute("category_index");
    }

    /**
     * @Route("/category/detail/{id}", name="category_detail")
     */
    public function categoryDetail($id)
    {
        $categorys = $this->em->getRepository(Category::class)->find($id);
        if ($categorys == null) {
            $this->addFlash("Error", "Category not found");
            return $this->redirectToRoute("category_index");
        }
        return $this->render(
            "category/detail.html.twig",
            [
                "category" => $categorys
            ]
        );
    }
    /**
     * @Route("/category/add", name="category_add")
     */

    public function categoryAdd(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $category->getImage();

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

            $category->setImage($imageName);


            $manager = $this->em->getManager();
            $manager->persist($category);
            $manager->flush();


            $this->addFlash("Success", "Add Category succeed !");
            return $this->redirectToRoute("category_index");
        }

        return $this->renderForm(
            "category/add.html.twig",
            [
                'form' => $form
            ]
        );
    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     */
    public function categoryEdit(Request $request, $id)
    {
        $category = $this->em->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['image']->getData();

            if ($file != null) {

                $image = $category->getImage();

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

                $category->setImage($imageName);
            }


            $manager = $this->em->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash("Success", "edit Category succeed !");
            return $this->redirectToRoute("category_index");
        }

        return $this->renderForm(
            "category/edit.html.twig",
            [
                'form' => $form
            ]
        );
    }
    /**
     * @Route("/category/search", name="search_category_name")
     */

    public function searchcategoryByName(CategoryRepository $repository, Request $request)
    {
        $name = $request->get("name");
        $categorys = $repository->searchCategory($name);
        return $this->render(
            "category/index.html.twig",
            [
                'categorys' => $categorys
            ]
        );
    }
}
