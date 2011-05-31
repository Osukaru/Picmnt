<?php

namespace SFM\PicmntBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SFM\PicmntBundle\Entity\Image;
use SFM\PicmntBundle\Entity\User;
use SFM\PicmntBundle\Repositories\ImageUp;
use FOS\UserBundle\Entity\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageController extends Controller
{
  
  /************************************************************************
   ************************ UPLOAD ACTTION ********************************
   ************************************************************************
   ************** Upload an image url into the database *******************
   ***********************************************************************/

  /**
   * @Route("/img/upload", name="img_upload")
   * @Template()
   */
  public function uploadAction()
  {
 
     $image = new Image();
    
     $imageUp = new ImageUp();

    //retrieving the user information 
    $user = $this->container->get('security.context')->getToken()->getUser();
    
    //insert the actual loged User
    $image->setUserId($user->getId());
        
    //calling the form
    $form = $this->get('form.factory')
      ->createBuilder('form', $imageUp)
      ->add('dataFile', 'file')
      //add('FieldName', 'type')
      ->getForm();
    
    //retrieving the request
    $request = $this->get('request');
 
   
    if ($request->getMethod() == 'POST'){
      $form->bindRequest($request);
      

      if ($form->isValid()) {


	//persist in the database
	/*	$em = $this->get('doctrine')->getEntityManager();     
   	
	$em->persist($image);
	$em->flush();
	*/
	
	$files=$request->files->get($form->getName());

	$uploadedFile=$files["dataFile"]["file"]; //"dataFile" is the name on the field

	//once you have the uploadedFile object there is some sweet functions you can run
	$uploadedFile->getPath();//returns current (temporary) path
	$uploadedFile->getOriginalName();
	$uploadedFile->getMimeType();

	//and most important is move(),
	$uploadedFile->move(
	  $_SERVER['DOCUMENT_ROOT']."/uploads",
	  $uploadedFile->getOriginalName());


	return $this->redirect($this->generateUrl('secure_home'));
	
      }
      else {

	return array('form' => $form->createView(),);

      }
    }
        
    return array('form' => $form->createView(),);
    
  }

 /**
   * @Route("/img/random", name="img_random")
   * @Template()
   */
  public function getRandomImageAction(){

    $rsm = new ResultSetMapping;
    $rsm->addEntityResult('SFM\PicmntBundle\Entity\Image','i');
    $rsm->addFieldResult('i', 'idImage','idImage');
    $rsm->addFieldResult('i','url','url');
    

    $image = new Image();

    $em = $this->get('doctrine')->getEntityManager();

    //$query = $em->createQuery('SELECT i.url, \''.rand().'\' rand FROM SFM\PicmntBundle\Entity\Image i ORDER BY rand');

    $query = $em->createNativeQuery('SELECT url, id_image AS idImage FROM Image ORDER BY rand() limit 1', $rsm);
    
    $images = $query->getResult();

    $image = $images[0];

    //    print_r($images);

    //    echo $image->getUrl();

    return array('image'=>$image->getUrl());

  }
 

   

}
