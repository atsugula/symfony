<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Retorna vista con los productos
    #[Route('/producto', name: 'app_producto')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->em->getRepository(Product::class)->findAllProductos();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );
        return $this->render('product/index.html.twig',['products' => $pagination]);
    }

    //Metodo para registrar un producto
    #[Route('/new/producto', name: 'new_producto')]
    public function create(Request $request){
        //instanciamos el producto y creamos el form
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product->setCreatedAt(new \DateTime());
            $product->setUpdatedAt(new \DateTime());
            $this->em->persist($product);
            $this->em->flush();
            $this->addFlash('success', '¡El producto se registro correctamente.!');
            return $this->redirectToRoute('app_producto');
        }

        return $this->render('product/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    //Metodo para actualizar el producto
    #[Route('/edit/producto/{id}', name: 'edit_producto')]
    public function edit($id, Request $request){
        //instanciamos el producto y creamos el form
        $product = $this->em->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('El producto solicitado no existe.');
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product->setUpdatedAt(new \DateTime());
            $this->em->flush();
            $this->addFlash('success', '¡El producto se actualizo correctamente.!');
            return $this->redirectToRoute('app_producto');
        }

        return $this->render('product/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }
    
    //Metodo para observar el producto
    #[Route('/show/producto/{id}', name: 'show_producto')]
    public function show($id)
    {
        $product = $this->em->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('El producto solicitado no existe.');
        }
        return $this->render('product/show.html.twig',['product' => $product]);
    }

    //Metodo para eliminar el producto
    #[Route('/remove/producto/{id}', name: 'remove_producto')]
    public function remove($id){
        $product = $this->em->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('El producto solicitado no existe.');
        }
        $this->em->remove($product);
        $this->em->flush();
        $this->addFlash('success', '¡El producto se eliminó correctamente.!');
        return $this->redirectToRoute('app_producto'); 
    }
}
