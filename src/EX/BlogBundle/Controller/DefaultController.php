<?php

namespace EX\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EX\BlogBundle\Entity\Articles;
use EX\BlogBundle\Form\ArticlesType;
use EX\BlogBundle\Entity\User;
use EX\BlogBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{

  public $liste_articles=[];
  public $listes=[];
  function __construct () {
  }


  public function indexAction(Request $request )
  {
    $session = $request->getSession();
    // $session->set('yoyo', 69290 );
    // $session->remove('user_id');
    // $session->clear();
    // print_r($session);

    $speudo="yog";



    $repository = $this->getDoctrine()
    ->getManager()
    ->getRepository('EXBlogBundle:Articles')
    ->findAll();


    // print_r($session);

    return $this->render('EXBlogBundle:Default:index.html.twig',array(
      'speudo'=>$speudo,
      'repertoire'=>$repository,
      'listes'=>$this->listes,
      'session'=>$session
    ));
  }
  public function viewAction($id,Request $request) {
    $session = $request->getSession();
    $this->liste_articles=$session->get('article_vu');
    $this->liste_articles[$id]=$id;
    $session->set( 'article_vu',$this->liste_articles );
    print_r($session);
    $em=$this->getDoctrine()->getManager();

    $article= $em->getRepository('EXBlogBundle:Articles')->find($id);

    return $this->render('EXBlogBundle:Default:page.html.twig',array(
      'article'=>$article,
      'session'=>$session
    ));
  }
  public function ajouterAction(Request $request){
    $article=new Articles ;
    $form=$this->get('form.factory')->create(ArticlesType::class, $article);

    if ($request->isMethod('POST')) {

      $form->handleRequest($request);

      if ($form->isValid()) {
        $em=$this->getDoctrine()
        ->getManager();
        $em->persist($article);
        $em->flush();
      }
    }
    return $this->render('EXBlogBundle:Default:ajoute.html.twig',array(
      'form' => $form->createView()
    ));

  }
  public function supprimerAction($id){
    $em=$this->getDoctrine()
    ->getManager();

    $article=$em->getRepository('EXBlogBundle:Articles')
    ->find($id);
    $em->remove($article);
    $em->flush();

    return $this->redirectToRoute('ex_blog_homepage');
  }
  public function modifierAction($id,Request $request){
    $em=$this->getDoctrine()
    ->getManager();


    $article=$em->getRepository('EXBlogBundle:Articles')
    ->find($id);

    $form=$this->get('form.factory')->create(ArticlesType::class, $article);

    if ($request->isMethod('POST')) {

      $form->handleRequest($request);

      if ($form->isValid()) {
        $em=$this->getDoctrine()
        ->getManager();
        $em->persist($article);
        $em->flush();
      }
    }
    return $this->render('EXBlogBundle:Default:modifier.html.twig',array(
      'form' => $form->createView()
    ));
  }
  public function inscriptionAction(Request $request){
    $user=new User ;
    $form=$this->get('form.factory')->create(UserType::class, $user);

    if ($request->isMethod('POST')) {

      $form->handleRequest($request);

      if ($form->isValid()) {
        $em=$this->getDoctrine()
        ->getManager();
        $em->persist($user);
        $em->flush();
      }
    }

    return $this->render('EXBlogBundle:Default:inscription.html.twig',array(
      'form' => $form->createView()
    ));
  }
  public function connexionAction(Request $request){
    $user=new User ;
    $form=$this->get('form.factory')->create(UserType::class, $user);

    if ($request->isMethod('POST')) {

      $form->handleRequest($request);

      if ($form->isValid()) {
        $data=$form->getData();
        $login=$data->getLogin();
        $password=$data->getPassword();

        $em=$this->getDoctrine()
        ->getManager();

        $user =$em->getRepository('EXBlogBundle:User')
        ->findOneByLogin($login);

        if($user==null){

          return $this->redirectToRoute('ex_blog_inscription');
        }
        if ($password==$user->getPassword()) {
          $session = $request->getSession();
          $session->set('login', $login );
          $session->set('password', $password );
          // var_dump($connexion);
          return $this->redirectToRoute('ex_blog_homepage');
        }
      }
    }




    return $this->render('EXBlogBundle:Default:connexion.html.twig',array(
      'form' => $form->createView(),
      'user' => $user
    ));
  }
  public function deconnexionAction(Request $request){
    $session = $request->getSession();
    // $session->remove('login');
    // $session->remove('password');
    $session->clear();

    return $this->redirectToRoute('ex_blog_homepage');

  }
}
