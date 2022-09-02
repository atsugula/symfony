<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Retorna vista con todas las categorias
    #[Route('/categoria', name: 'app_categoria')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->em->getRepository(Category::class)->findAllCategory();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );
        return $this->render('category/index.html.twig',['categorys' => $pagination]);
    }

    //Metodo para registrar una categoria
    #[Route('/new/categoria', name: 'new_categoria')]
    public function create(Request $request){
        //instanciamos la categoria y creamos el form
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $category->setCreatedAt(new \DateTime());
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', '¡La categoría se registro correctamente.!');
            return $this->redirectToRoute('app_categoria');
        }

        return $this->render('category/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    //Metodo para actualizar la categoria
    #[Route('/edit/categoria/{id}', name: 'edit_categoria')]
    public function edit($id, Request $request){
        $category = $this->em->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('La categoría solicitada no existe.');
        }
        //instanciamos la categoria y creamos el form
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $category->setUpdatedAt(new \DateTime());
            $this->em->flush();
            $this->addFlash('success', '¡La categoría se actualizo correctamente.!');
            return $this->redirectToRoute('app_categoria');
        }

        return $this->render('category/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }
    
    //Metodo para observar la categoria
    #[Route('/show/categoria/{id}', name: 'show_categoria')]
    public function show($id): Response
    {
        $category = $this->em->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('La categoría solicitada no existe.');
        }
        return $this->render('category/show.html.twig',['category' => $category]);
    }

    //Metodo para eliminar la categoria, si esta asignada, no se elimina
    #[Route('/remove/categoria/{id}', name: 'remove_categoria')]
    public function remove($id){
        $category = $this->em->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('La categoría solicitada no existe.');
        }
        $catWithProducts = $this->em->getRepository(Category::class)->findProductos($category->getId());
        if(count($catWithProducts) == 0){
            $this->em->remove($category);
            $this->em->flush();
            $this->addFlash('success', '¡La categoría se eliminó correctamente.!');
            return $this->redirectToRoute('app_categoria');
        }
        $this->addFlash('error', '¡La categoría tiene productos asignados.!');
        return $this->redirectToRoute('app_categoria');
    }
}
