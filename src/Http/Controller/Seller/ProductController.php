<?php

namespace App\Http\Controller\Seller;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepository;
use App\Domain\Product\ProductTypeRepository;
use App\Domain\Shop\Shop;
use App\Domain\Shop\ShopRepository;
use App\Http\Form\Product\ProductForm;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/ma-boutique", name: "seller_")]
class ProductController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ShopRepository         $shopRepository,
        private readonly ProductRepository      $productRepository,
        private readonly ProductTypeRepository  $productTypeRepository,
        private readonly PaginatorInterface $paginator
    ){}

    #[Route(path: "/{id}/produits", name: "edit_products", requirements: ["id" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function products(Shop $shop, Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $product->setShop($shop);
            $this->checkUnitDiscountPrice($product);
            [$product, $response] = $this->setProductImages($images, $product, $shop);
            if($response) {
                return $response;
            }
            $product->getType()->addProduct($product);
            $this->em->persist($product);
            $this->em->flush();
            $this->addFlash('success', 'Le produit a bien été créé');
        }
        $products = $this->paginator->paginate(
            $this->productRepository->findAllByShopQuery($shop),
            $request->query->getInt('page', 1),
            6
        );
        $total_products = count($products);
        return $this->render('seller/products.html.twig', [
            'shop' => $shop,
            'products' => $products,
            'total_products' => $total_products,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: "/{id}/produits/{product}/modifier", name: "edit_product", requirements: ["id" => "[0-9\-]*", "product" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function editProduct(Request $request): Response
    {
        $product = $this->productRepository->find($request->attributes->get('product'));
        $shop = $this->shopRepository->find($request->attributes->get('id'));
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $product->setName($product->getName())
                ->setDescription($product->getDescription())
                ->setUnitPrice($product->getUnitPrice())
                ->setType($form->get('type')->getData());
            $this->checkUnitDiscountPrice($product);
            [$product, $response] = $this->setProductImages($images, $product, $shop);
            if($response) {
                return $response;
            }
            $product->getType()->addProduct($product);
            $this->em->persist($product);
            $this->em->flush();
            return $this->redirectToRoute('seller_edit_products', ['id' => $shop->getId()]);
        }
        return $this->render("seller/edit_product.html.twig", [
            'shop' => $shop,
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: "/{id}/produits/{product}/supprimer", name: "delete_product", requirements: ["id" => "[0-9\-]*", "product" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function delete_product(Request $request): RedirectResponse
    {
        $product = $this->productRepository->find($request->attributes->get('product'));
        $shop = $this->shopRepository->find($request->attributes->get('id'));
        $this->em->remove($product);
        $this->em->flush();
        $this->addFlash('warning', 'Le produit a bien été supprimé');
        return $this->redirectToRoute('seller_edit_products', ["id" => $shop->getId()]);
    }

    private function setProductImages(array $images, Product $product, Shop $shop): array
    {
        $limit = count($images);
        for ($i = 0; $i < $limit; $i++) {
            if ($images[$i]) {
                $newFilename = sprintf("%s-%s-%s-%s.%s", $product->getName(), $shop->getId(), $shop->getName(), $i, $images[$i]->guessExtension());
                try {
                    $images[$i]->move(
                        $this->getParameter('uploads/products'),
                        $newFilename
                    );
                } catch (FileException) {
                    $response = new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR);
                    return [null, $response];
                }
                $product_images = $product->getImages();
                $product_images[] = $newFilename;
                $product->setImages($product_images);
            }
        }
        return [$product, null];
    }

    private function checkUnitDiscountPrice(Product $product): void
    {
        if ($product->getUnitPriceDiscount() == null) {
            $product->setUnitPriceDiscount(null);
        } else {
            $product->setUnitPriceDiscount($product->getUnitPriceDiscount());
        }
    }
}