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
            3 /*limit per page*/
        );
        return $this->render('category/index.html.twig',['categorys' => $pagination, 'i' => 0]);
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
            return $this->redirectToRoute('app_categoria');
        }

        return $this->render('category/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/categoria/{id}', name: 'edit_categoria')]
    public function edit($id){
        $category = $this->em->getRepository(Category::class)->find($id);
        $category->setName('Categoria 4');
        $category->setUpdatedAt(new \DateTime());
        $this->em->flush();
        return new JsonResponse(['success' => true]);
        // return $this->render('category/create.html.twig');
    }

    #[Route('/show/categoria/{id}', name: 'show_categoria')]
    public function show($id): Response
    {
        $category = $this->em->getRepository(Category::class)->find($id);
        return $this->render('category/index.html.twig',['category' => $category]);
    }

    #[Route('/remove/categoria/{id}', name: 'remove_categoria')]
    public function remove($id){
        $category = $this->em->getRepository(Category::class)->find($id);
        $this->em->remove($category);
        $this->em->flush();
        return $this->redirectToRoute('app_categoria');
    }
}
