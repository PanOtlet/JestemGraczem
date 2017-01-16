<?php

namespace NewsBundle\Controller;

use NewsBundle\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class DefaultController extends Controller
{

    protected $color = "green";

    /**
     * @Route("/article/{id}-{title}", name="article.id")
     * @param $id
     * @param $title
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function articleAction($id, $title = null)
    {
        $article = $this->getDoctrine()->getRepository('NewsBundle:News')->findOneBy(['id' => $id]);

        if ($article == NULL) {
            $this->addFlash(
                'error',
                'Kurde, nie znaleźliśmy tego co poszukujesz :('
            );
            throw $this->createNotFoundException('Kurde, nie znaleźliśmy tego co poszukujesz :(');
        }

        if ($title == null || $title != str_replace(' ', '-', $article->getTitle())) {
            return $this->redirectToRoute('article.id', [
                'id' => $id,
                'title' => str_replace(' ', '-', $article->getTitle())
            ]);
        }

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle($article->getTitle() . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', substr($article->getIntroduction(), 0, 120))
            ->addMeta('property', 'og:title', $article->getTitle())
            ->addMeta('property', 'og:description', substr($article->getIntroduction(), 0, 120))
            ->addMeta('property', 'og:image', $this->container->get('vich_uploader.templating.helper.uploader_helper')->asset($article, 'imageFile'))
            ->addMeta('property', 'og:url', $this->get('router')->generate('article.id', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render($this->getParameter('theme') . '/news/news.html.twig', [
            'color' => $this->color,
            'article' => $article
        ]);
    }

    /**
     * @Route("/add", name="article.add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Dodaj artykuł :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Dodaj swój własny artykuł na stronę!')
            ->addMeta('property', 'og:title', 'Dodaj artykuł')
            ->addMeta('property', 'og:description', 'Dodaj swój własny artykuł na stronę!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('article.add', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $article = new News();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, [
                'label' => 'article.title'
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_link' => true,
            ])
            ->add('introduction', TextareaType::class, [
                'label' => 'article.introduction'
            ])
            ->add('body', CKEditorType::class, [
                'base_path' => 'ckeditor',
                'js_path' => 'ckeditor/ckeditor.js',
                'label' => 'article.body'
            ])
            ->add('source', TextareaType::class, [
                'label' => 'article.source'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'article.add',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $article->setAuthor($this->getUser()->getId());
            $article->setTitle($form->get('title')->getViewData());
            $article->setIntroduction($form->get('introduction')->getViewData());
            $article->setBody($form->get('body')->getViewData());
            $article->setSource($form->get('source')->getViewData());
            $article->setDate(new \DateTime("now"));
            $article->setAccepted(false);
            if ($this->getUser()->getEditor()) {
                $article->setAccepted(true);
            }
            $article->setPromoted(false);
            $em->persist($article);
            $em->flush();

            $this->addFlash(
                'success',
                'Dodano artykuł! Teraz Twój artykuł pojawił się w poczekalni i oczekuje akceptacji przez redakcję, by pojawić się na głównej!'
            );
            return $this->redirectToRoute('article.all');
        }

        return $this->render($this->getParameter('theme') . '/news/add.html.twig', [
            'color' => $this->color,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/wszystkie/{page}", name="article.all")
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function allAction($page = 0)
    {
        if ($page < 0 || !is_numeric($page)) {
            return $this->redirectToRoute('articles');
        }

        $em = $this->getDoctrine()->getRepository('NewsBundle:News');
        $query = $em->createQueryBuilder('p')
            ->setMaxResults(10)
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->leftJoin("AppBundle:User", "u", "WITH", "u.id=p.author")
            ->addSelect('u.username')
            ->select('p.id, p.author, p.title, p.date, p.introduction, p.body, p.source, u.username, p.promoted, p.imageName')
            ->getQuery();

        $articles = $query->getResult();

        if ($articles == NULL) {
            throw $this->createNotFoundException('Kurde, nie znaleźliśmy tego co poszukujesz :(');
        }

        $promoted = $em->createQueryBuilder('e')
            ->where('e.promoted = true')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Najnowsze wieści ze świata gier! Strona ' . $page . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Najnowsze wieści ze świata gier i nie tylko! Strona ' . $page)
            ->addMeta('property', 'og:title', 'Najnowsze wieści ze świata gier! Strona ' . $page)
            ->addMeta('property', 'og:description', 'Najnowsze wieści ze świata gier i nie tylko! Strona ' . $page)
            ->addMeta('property', 'og:url', $this->get('router')->generate('articles', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render($this->getParameter('theme') . '/news/index.html.twig', [
            'color' => $this->color,
            'articles' => $articles,
            'page' => $page,
            'promoted' => $promoted
        ]);
    }

    /**
     * @Route("/{page}", name="articles")
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page = 0)
    {
        if ($page < 0 || !is_numeric($page)) {
            return $this->redirectToRoute('articles');
        }

        $em = $this->getDoctrine()->getRepository('NewsBundle:News');
        $query = $em->createQueryBuilder('p')
            ->where('p.accepted = true')
            ->setMaxResults(10)
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->leftJoin("AppBundle:User", "u", "WITH", "u.id=p.author")
            ->addSelect('u.username')
            ->select('p.id, p.author, p.title, p.date, p.introduction, p.body, p.source, u.username, p.promoted, p.imageName')
            ->getQuery();

        $articles = $query->getResult();

        if ($articles == NULL) {
            throw $this->createNotFoundException('Kurde, nie znaleźliśmy tego co poszukujesz :(');
        }

        $promoted = $em->createQueryBuilder('e')
            ->where('e.promoted = true')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Najnowsze wieści ze świata gier! Strona ' . $page . ' :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Najnowsze wieści ze świata gier i nie tylko! Strona ' . $page)
            ->addMeta('property', 'og:title', 'Najnowsze wieści ze świata gier! Strona ' . $page)
            ->addMeta('property', 'og:description', 'Najnowsze wieści ze świata gier i nie tylko! Strona ' . $page)
            ->addMeta('property', 'og:url', $this->get('router')->generate('articles', ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render($this->getParameter('theme') . '/news/index.html.twig', [
            'color' => $this->color,
            'articles' => $articles,
            'page' => $page,
            'promoted' => $promoted
        ]);
    }
}
